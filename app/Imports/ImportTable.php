<?php

namespace App\Imports;

use App\Models\ERP;
use App\Models\Table;
use App\Models\DetailTable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ImportTable implements ToCollection
{
    protected $variable;
    protected $tableRefs;
    protected $fieldRefs;

    public function __construct($variable)
    {
        $this->variable = ERP::where('Initials', $variable)->value('ERPID');
        $this->tableRefs = Table::where('ERPID', $this->variable)->get();
        $this->fieldRefs = [];

        foreach ($this->tableRefs as $table) {
            $this->fieldRefs[$table->Name] = $table->fields->pluck('FieldID', 'Name');
        }
        $this->tableRefs = $this->tableRefs->pluck('TableID', 'Name');
    }

    public function collection(Collection $collection)
    {
        $errors = [];
        $columnNames = $collection->shift()->toArray(); // Remove first row and convert to array
        $requiredColumns = ['TableName', 'FieldName', 'Nullable', 'DataType'];
        foreach ($requiredColumns as $columnName) {
            if (!in_array($columnName, $columnNames)) {
                session()->flash('danger', 'Import Gagal Dilakukan. Periksa Kembali File Excel.');
                return back()->withInput();
            }
        }

        $collection->each(function ($row, $index) use ($columnNames, &$errors) {

            $requiredColumns = ['TableName', 'FieldName', 'Nullable', 'DataType'];
            foreach ($requiredColumns as $columnName) {
                $columnIndex = array_search($columnName, $columnNames);
                if (!isset($row[$columnIndex]) || is_null($row[$columnIndex])) {
                    $errors[] = $index + 2;
                }
            }
        });
        $errors = array_unique($errors);

        if (!empty($errors)) {
            $errorMessage = "Baris " . implode(', ', $errors) . " Bermasalah";
            session()->flash('danger', 'Import Gagal Dilakukan. ' . $errorMessage);
            return back()->withInput();
        }

        // If there are no errors, proceed to create fields
        $collection->each(function ($row, $index) use ($columnNames) {
            $tableData = [];
            $fieldData = [];

            foreach ($columnNames as $index => $columnName) {
                switch ($columnName) {
                    case 'TableName':
                        $tableData['Name'] = $row[$index];
                        break;
                    case 'TableDescription':
                        $tableData['Description'] = $row[$index];
                        break;
                    case 'FieldName':
                        $fieldData['Name'] = $row[$index];
                        break;
                    case 'FieldDescription':
                        $fieldData['Description'] = $row[$index];
                        break;
                    case 'Nullable':
                        $fieldData['AllowNull'] = $row[$index] !== 'N';
                        break;
                    case 'DataType':
                        $fieldData['DataType'] = $row[$index];
                        break;
                    case 'Default Value':
                        $fieldData['DefaultValue'] = $row[$index];
                        break;
                    case 'Table Ref':
                        $fieldData['TableIDRef'] = $this->tableRefs[$row[$index]] ?? null;
                        break;
                    case 'Field Ref':
                        $fieldData['FieldIDRef'] = $this->fieldRefs[$row[$index]] ?? null;
                        break;
                }
            }
            $tableID = $this->tableRefs[$tableData['Name']] ?? null;

            if (!$tableID) {
                $tableData['ERPID'] = $this->variable;
                $newTable = Table::create($tableData);
                $this->tableRefs[$newTable->Name] = $newTable->TableID;
                $tableID = $newTable->TableID;
            } else {
                $checkTable = Table::find($tableID);
                $checkTable->update([
                    'Name' => $tableData['Name'],
                    'Description' => $tableData['Description']
                ]);
            }

            $fieldData['TableID'] = $tableID;
            $fieldID = $this->fieldRefs[$tableData['Name']][$fieldData['Name']] ?? null;
            if ($fieldID) {
                $checkField = DetailTable::find($fieldID);
                $checkField->update([
                    'TableID' => $fieldData['TableID'],
                    'Name' => $fieldData['Name'],
                    'Description' => $fieldData['Description'],
                    'DataType' => $fieldData['DataType'],
                    'AlowNull' => $fieldData['AllowNull'],
                    'DefaultValue' => $fieldData['DefaultValue'],
                    'TableIDRef' => $fieldData['TableIDRef'],
                    'FieldIDRef' => $fieldData['FieldIDRef'],
                ]);
                session()->flash('info', 'Terdapat Beberapa Field Yang Terupdate.');
            } else {
                $newField = DetailTable::create($fieldData);
                $this->fieldRefs[$tableData['Name']][$newField->Name] = $newField->FieldID;
            }
        });

        session()->flash('success', 'File Berhasil Di-import.');
    }
}
