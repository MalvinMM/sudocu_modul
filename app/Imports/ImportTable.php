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
        $this->tableRefs = Table::pluck('TableID', 'Name');
        $this->fieldRefs = DetailTable::pluck('FieldID', 'Name');
    }

    public function collection(Collection $collection)
    {
        $errors = [];
        $columnNames = $collection->shift()->toArray(); // Remove first row and convert to array

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
                    case 'DefaultValue':
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
                $checkTable = Table::create($tableData);
                $this->tableRefs[$checkTable->Name] = $checkTable->TableID;
                $tableID = $checkTable->TableID;
            }

            $fieldData['TableID'] = $tableID;
            $fieldID = $this->fieldRefs[$fieldData['Name']] ?? null;
            if (!$fieldID) {
                $checkField = DetailTable::create($fieldData);
                $this->fieldRefs[$checkField->Name] = $checkField->FieldID;
            } else {
                DetailTable::create($fieldData);
            }
        });

        session()->flash('success', 'File Berhasil Di-import. Tabel Ditambahkan');
    }
}
