<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\ImportExport\CsvExporter;
use App\Contracts\ImportExport\CsvImporter;
use App\Contracts\ImportExport\EvansImporter;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImportExport\ExportRequest;
use App\Http\Requests\ImportExport\ImportRequest;
use App\Models\RabbitBreeder;
use App\Models\Ledger\Entry as LedgerEntry;

class ImportExportController extends Controller
{
    public function import(ImportRequest $request, CsvImporter $csvImporter, EvansImporter $evansImporter)
    {
        $file = $request->file('import');
        /* @var $file \Illuminate\Http\UploadedFile */
        switch (strtolower($file->getClientOriginalExtension())) {
            case 'csv':
                $data = $csvImporter->parseBreeders($file);
                break;
            case 'html':
            case 'htm':
            default:
                $data = $evansImporter->parseBreeders($file);
        }
        return response()->json([
            'breeders' => $data->map(function (RabbitBreeder $breeder) {
            	return [
            		'sex' => $breeder->sex,
            		'name' => $breeder->name,
            		'tattoo' => $breeder->tattoo,
            		'cage' => $breeder->cage,
            		'color' => $breeder->color,
            		'breed' => $breeder->breed,
            		'weight' => $breeder->weight,
            		'date_of_birth' => $breeder->date_of_birth,
            		'acquired' => $breeder->aquired,
            		'registration_number' => $breeder->registration_number,
            		'champion_number' => $breeder->champion_number,
            		'legs' => $breeder->legs,
            		'status' => $breeder->status,
            		'status_date' => $breeder->status_date,
            		'father_name' => $breeder->father_name,
            		'mother_name' => $breeder->mother_name
            	];
            }),
        ]);
    }
    
    public function importLedgers(ImportRequest $request, CsvImporter $csvImporter, EvansImporter $evansImporter)
    {
    	$file = $request->file('import');
    	switch (strtolower($file->getClientOriginalExtension())) {
    		case 'csv':
    			$data = $csvImporter->parseLedgers($file);
    			break;
    		case 'html':
    		case 'htm':
    		default:
    			$data = $evansImporter->parseLedgers($file);
    	}
    	return response()->json([
    			'ledgers' => $data->map(function (LedgerEntry $ledger) {
    			return [
    				'date' => $ledger->date,
    				'name' => $ledger->name,
    				'amount' => $ledger->amount,
    				'debit' => $ledger->debit,
    				'description' => $ledger->description
    			];
    		}),
    	]);
    }

    public function export(ExportRequest $request, CsvExporter $csvExporter)
    {
        $rabbits = $request->user()->breeders();
        if ($request->has('ids')) {
            $rabbits->where('id', 'IN', $request->input('ids'));
        }
        switch ($request->input('type', 'csv')) {
            case 'csv':
            default:
                return response($csvExporter->exportBreeders($rabbits->get()), 200, [
                    'content-type' => 'text/csv; charset=UTF-8',
                    'content-disposition' => 'attachment; filename=hutch-export.csv'
                ]);
        }
    }
    
    public function exportLedgers(ExportRequest $request, CsvExporter $csvExporter)
    {
    	$ledgers = $request->user()->ledger()->get();
    	if($request->has('debit-type'))
    	{
    		$type = $request->input('debit-type');
    		if($type == 'income')
    		{
    			for ($i = 0; $i < count($ledgers); $i++)
    			{
    				$ledger = $ledgers[$i];
    				if($ledger->debit != 1)
    				{
    					unset($ledgers[$i]);
    				}
    			}
    		}
    		else if($type == 'expense')
    		{
    			for ($i = 0; $i < count($ledgers); $i++)
    			{
    				$ledger = $ledgers[$i];
    				if($ledger->debit != 0)
    				{
    					unset($ledgers[$i]);
    				}
    			}
    			
    		}
    	}
    	switch ($request->input('type', 'csv')) {
    		case 'csv':
    		default:
    			return response($csvExporter->exportLedgers($ledgers), 200, [
    				'content-type' => 'text/csv; charset=UTF-8',
    				'content-disposition' => 'attachment; filename=hutch-ledger-export.csv'
    			]);
    	}
    }
}
