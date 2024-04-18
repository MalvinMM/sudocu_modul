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
            // dd($columnNames);
            $tableData = [];
            $fieldData = [];
            foreach ($columnNames as $index => $columnName) {
                switch ($columnName) {
                    case 'TableName':
                        $tableData['Name'] = (string) $row[$index];
                        break;
                    case 'TableDescription':
                        $tableData['Description'] = (string) $row[$index];
                        break;
                    case 'FieldName':
                        $fieldData['Name'] = (string) $row[$index];
                        break;
                    case 'FieldDescription':
                        $fieldData['Description'] = (string) $row[$index];
                        break;
                    case 'Nullable':
                        $fieldData['AllowNull'] = $row[$index] !== 'N';
                        break;
                    case 'DataType':
                        $fieldData['DataType'] = $row[$index];
                        break;
                    case 'Default Value':
                    case 'DefaultValue':
                        $fieldData['DefaultValue'] = $row[$index];
                        break;
                    case 'Table Ref':
                    case 'TableRef':
                        // dd($row[$index]);
                        if ($row[$index]) {
                            $fieldData['TableIDRef'] = $this->tableRefs[$row[$index]] ?? 'none';
                            $fieldData['TableRefName'] = $row[$index];
                        } else {
                            $fieldData['TableIDRef'] = null;
                        }
                        break;
                    case 'Field Ref':
                    case 'FieldRef':
                        if ($row[$index]) {
                            // dd($fieldData['TableRefName']);
                            $fieldData['FieldIDRef'] = $this->fieldRefs[$fieldData['TableRefName']][$row[$index]] ?? 'none';
                            $fieldData['FieldRefName'] = $row[$index];
                        } else {
                            $fieldData['FieldIDRef'] = null;
                        }
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
                    'Description' => ($tableData['Description'] == "") ? $checkTable->Description : $tableData['Description']
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
                    'AllowNull' => $fieldData['AllowNull'],
                    'DefaultValue' => $fieldData['DefaultValue'],
                    'TableIDRef' => $fieldData['TableIDRef'],
                    'FieldIDRef' => $fieldData['FieldIDRef'],
                ]);
            } else {
                if ($fieldData['TableIDRef'] == 'none') {
                    $newTable = Table::create([
                        'Name' => $fieldData['TableRefName'],
                        'ERPID' => $this->variable
                    ]);
                    $fieldData['TableIDRef'] = $newTable->TableID;
                    $this->tableRefs[$newTable->Name] = $newTable->TableID;
                }
                unset($fieldData['TableRefName']);

                if ($fieldData['FieldIDRef'] == 'none') {
                    $newField = DetailTable::create([
                        'TableID' => $fieldData['TableIDRef'],
                        'Name' => $fieldData['FieldRefName'],
                        'DataType' => 'Integer',
                        'AllowNull' => false,
                    ]);
                }
                unset($fieldData['FieldRefName']);
                $newField = DetailTable::create([
                    'TableID' => $fieldData['TableID'],
                    'Name' => $fieldData['Name'],
                    'Description' => $fieldData['Description'],
                    'DataType' => $fieldData['DataType'],
                    'AllowNull' => $fieldData['AllowNull'],
                    'DefaultValue' => $fieldData['DefaultValue'],
                    'TableIDRef' => $fieldData['TableIDRef'],
                    'FieldIDRef' => $fieldData['FieldIDRef'],
                ]);
                $this->fieldRefs[$tableData['Name']][$newField->Name] = $newField->FieldID;
            }
        });

        session()->flash('success', 'File Berhasil Di-import.');
    }
}
