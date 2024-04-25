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
        //Inisasi variable
        $this->variable = ERP::where('Initials', $variable)->value('ERPID');
        // variabel = ERP
        $this->tableRefs = Table::where('ERPID', $this->variable)->get();
        // $this->tableRefs = Collection yang keys nya nama tabel, values nya id tabel. Untuk mencari id tabel.
        $this->fieldRefs = [];
        // $this->fieldRefs = 3D array index nya : [Nama Tabel][Nama Field]. Untuk mencari id field.

        foreach ($this->tableRefs as $table) {
            $this->fieldRefs[$table->Name] = $table->fields->pluck('FieldID', 'Name');
        }
        $this->tableRefs = $this->tableRefs->pluck('TableID', 'Name');
    }

    public function collection(Collection $collection) // Cek kolom wajib -> cari tabelrefs dan fieldrefs -> input/update table yang ada di tablerefs dan fieldrefs (Pakai data default) -> input/update table yang ada di excel.
    {
        $errors = [];
        $columnNames = $collection->shift()->toArray(); // array nama" kolom (baris prtama di excel)

        $requiredColumns = ['TableName', 'FieldName', 'Nullable', 'DataType']; // nama" kolom yang wajib ada.
        foreach ($requiredColumns as $columnName) {
            if (!in_array($columnName, $columnNames)) {
                session()->flash('danger', 'Import Gagal Dilakukan. Periksa Kembali File Excel.');
                return back()->withInput();
            }
        }

        // Cek kalau ada baris yang kolom wajibnya kosong (kalau kosong return errornya)
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

        // Inisiasi variabel baru. untuk menyimpan nama" tableRefs dan fieldRefs yang ada isinya di excel (untuk di insert duluan)
        $tableRefs = [];
        $fieldRefs = [];
        // $tableNames = []; // Inisiasi variabel tableNames untuk menyimpan semua nama tabel (kolom pertama) yang ada di excel ini.

        $collection->each(function ($row) use (&$tableRefs, &$fieldRefs, &$tableNames) {
            if ($row[7]) {
                $tableRefs[] = $row[7];
                $fieldRefs[] = $row[8];
            }

            // if (!in_array($row[0], $tableNames)) {
            //     $tableNames[] = $row[0];
            // }
        });

        // Buat nama-nama field yang ada jadi satu array (tidak terpisah dengan tabel lagi dan tidak menjadi key melainkan value).
        $currentFields = collect($this->fieldRefs)->map(function ($collection) {
            return $collection->keys()->all();
        })->flatten()->all();

        // Loop 1 : insert/update tabel dan field yang menjadi FK di tabel lain (ada di tableRefs & fieldRefs). Data diisi dengan default primary key.
        foreach ($tableRefs as $index => $value) {

            // Kalau cuma ada tableRefs, tidak ada fieldRefs --> Error --> Kemungkinan kesalahan penulisan excel.
            // dd($fieldRefs, $tableRefs);
            if ($fieldRefs[$index] == null) {
                session()->flash('danger', 'Import Gagal Dilakukan. Cek kembali kolom Table dan Field Refs. Keyword : ' . $value);
                return back()->withInput();
            }

            // Checking nama tabel sudah ada atau belum. Kalau sudah ada -> Update, kalau belum -> Insert.
            if (!in_array($value, $this->tableRefs->keys()->all())) {
                $newTable = Table::create([
                    'ERPID' => $this->variable,
                    'Name' => $value,
                    'Description' => null
                ]);
            } else {
                $newTable = Table::find($this->tableRefs[$value]);
                $newTable->update([
                    'Name' => $value,
                    'Description' => null
                ]);
            }
            $this->tableRefs[$newTable->Name] = $newTable->TableID;
            $tableID = $newTable->TableID;

            // dd($tableRefs);
            // Checking nama field sudah ada atau belum. Kalau sudah ada -> Update, kalau belum -> Insert.
            if (in_array($fieldRefs[$index], $currentFields) && in_array($value, array_unique(array_keys($this->fieldRefs)))) {

                $newField = DetailTable::find($this->fieldRefs[$value][$fieldRefs[$index]]);
                $newField->update([
                    'Name' => $fieldRefs[$index],
                    'Description' => null,
                    'DataType' => 'bigint',
                    'AllowNull' => false,
                    'DefaultValue' => null,
                    'TableIDRef' => null,
                    'FieldIDRef' => null
                ]);
            } else {
                $newField = DetailTable::create([
                    'TableID' => $tableID,
                    'Name' => $fieldRefs[$index],
                    'Description' => null,
                    'DataType' => 'bigint',
                    'AllowNull' => false,
                    'DefaultValue' => null,
                    'TableIDRef' => null,
                    'FieldIDRef' => null
                ]);
                $currentFields[] = $newField->Name;
            }
            $this->fieldRefs[$value][$newField->Name] = $newField->FieldID;
        }

        // Loop 2 : insert/update setiap baris yang ada pada excel (sekaligus update primary key yang tadi hanya dibuat untu keperluan TableRefs & FieldRefs)
        $collection->each(function ($row, $index) use ($columnNames, $tableRefs, $fieldRefs) {

            $tableData = [];
            $fieldData = [];
            // Cek setiap kolom dan masukkan ke dalam array dengan key (nama kolom di DB) dan value (isi yang akan diinput ke DB)
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
                        $fieldData['TableIDRef'] = null;
                        if ($row[$index]) {
                            $fieldData['TableIDRef'] = $this->tableRefs[$row[$index]] ?? null;
                            $fieldData['TableRefName'] = $row[$index];
                        }
                        break;
                    case 'Field Ref':
                    case 'FieldRef':
                        $fieldData['FieldIDRef'] = null;
                        if ($row[$index]) {
                            $fieldData['FieldIDRef'] = $this->fieldRefs[$fieldData['TableRefName']][$row[$index]] ?? null;
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

            $fieldID = $this->fieldRefs[$tableData['Name']][$fieldData['Name']] ?? null;
            $fieldData['TableID'] = $tableID;

            if ($fieldID !== null) {
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
            } else {
                $newField = DetailTable::create($fieldData);
                $this->fieldRefs[$tableData['Name']][$newField->Name] = $newField->FieldID;
            }
        });

        session()->flash('success', 'File Berhasil Di-import.');
    }
}







// ------------------------- DUMP (Mungkin bisa berguna) -------------------------

// Loop 2 : table & field yang menjadi FK di tabel lain (ada di tableRefs & fieldRefs) dan data tabelnya terdapat di excel ini. [Memisahkan pembuatan tabel yang ada di excel dan tidak -- Menggunakan $tableNames] 
        // $collection->each(function ($row, $index) use ($columnNames, $tableRefs, $fieldRefs) {
        //     if (in_array($row[0], $tableRefs) && in_array($row[2], $fieldRefs)) {
        //         $tableData = [];
        //         $fieldData = [];
        //         foreach ($columnNames as $index => $columnName) {
        //             switch ($columnName) {
        //                 case 'TableName':
        //                     $tableData['Name'] = (string) $row[$index];
        //                     break;
        //                 case 'TableDescription':
        //                     $tableData['Description'] = (string) $row[$index];
        //                     break;
        //                 case 'FieldName':
        //                     $fieldData['Name'] = (string) $row[$index];
        //                     break;
        //                 case 'FieldDescription':
        //                     $fieldData['Description'] = (string) $row[$index];
        //                     break;
        //                 case 'Nullable':
        //                     $fieldData['AllowNull'] = $row[$index] !== 'N';
        //                     break;
        //                 case 'DataType':
        //                     $fieldData['DataType'] = $row[$index];
        //                     break;
        //                 case 'Default Value':
        //                 case 'DefaultValue':
        //                     $fieldData['DefaultValue'] = $row[$index];
        //                     break;
        //                 case 'Table Ref':
        //                 case 'TableRef':
        //                     // dd($row[$index]);
        //                     $fieldData['TableIDRef'] = null;
        //                     if ($row[$index]) {
        //                         $fieldData['TableIDRef'] = $this->tableRefs[$row[$index]] ?? null;
        //                         $fieldData['TableRefName'] = $row[$index];
        //                     }
        //                     break;
        //                 case 'Field Ref':
        //                 case 'FieldRef':
        //                     $fieldData['FieldIDRef'] = null;
        //                     if ($row[$index]) {
        //                         $fieldData['FieldIDRef'] = $this->fieldRefs[$fieldData['TableRefName']][$row[$index]] ?? null;
        //                     }
        //                     // dd($fieldData['TableRefName']);
        //                     break;
        //             }
        //         }
        //         $tableID = $this->tableRefs[$tableData['Name']] ?? null;
        //         if (!$tableID) {
        //             $tableData['ERPID'] = $this->variable;
        //             $newTable = Table::create($tableData);
        //             $this->tableRefs[$newTable->Name] = $newTable->TableID;
        //             $tableID = $newTable->TableID;
        //         } else {
        //             $checkTable = Table::find($tableID);
        //             $checkTable->update([
        //                 'Name' => $tableData['Name'],
        //                 'Description' => ($tableData['Description'] == "") ? $checkTable->Description : $tableData['Description']
        //             ]);
        //         }

        //         $fieldID = $this->fieldRefs[$tableData['Name']][$fieldData['Name']] ?? null;
        //         $fieldData['TableID'] = $tableID;

        //         if ($fieldID) {
        //             $newField = DetailTable::find($fieldID);
        //             $newField->update([
        //                 'TableID' => $fieldData['TableID'],
        //                 'Name' => $fieldData['Name'],
        //                 'Description' => $fieldData['Description'],
        //                 'DataType' => $fieldData['DataType'],
        //                 'AllowNull' => $fieldData['AllowNull'],
        //                 'DefaultValue' => $fieldData['DefaultValue'],
        //                 'TableIDRef' => $fieldData['TableIDRef'],
        //                 'FieldIDRef' => $fieldData['FieldIDRef'],
        //             ]);
        //         } else {
        //             $newField = DetailTable::create($fieldData);
        //             $this->fieldRefs[$tableData['Name']][$newField->Name] = $newField->FieldID;
        //         }
        //         unset($fieldData['TableRefName']);
        //         $this->fieldRefs[$tableData['Name']][$newField->Name] = $newField->FieldID;
        //     }
        // });