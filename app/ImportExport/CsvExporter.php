<?php

namespace App\ImportExport;

use App\Contracts\ImportExport\CsvExporter as CsvExporterContract;
use App\Models\RabbitBreeder;
use App\Models\Ledger\Entry as LedgerEntry;
use Carbon\Carbon;

class CsvExporter implements CsvExporterContract
{
    public function exportBreeders($breeders)
    {
        $keys = ['sex', 'name', 'tattoo', 'cage', 'color', 'breed', 'weight', 'date_of_birth',
                 'aquired', 'registration_number', 'champion_number', 'legs', 'status', 'status_date'];

        $fh = fopen('php://memory', 'w+');
        fputcsv($fh, ['Sex', 'Name', 'Tattoo', 'Cage', 'Color', 'Breed', 'Weight',
                      'DoB', 'Acquired', 'Reg #', 'Champ #', 'Legs', 'Status', 'Status Date'], ',');
        $breeders->each(function (RabbitBreeder $breeder) use ($fh, $keys) {
            fputcsv($fh, array_map(function ($key) use ($breeder) {
                if($key == 'status')
                {
                	if($breeder->archived == 1)
                	{
                		$status = 'archived';
                	}
                	elseif($breeder->died == 1)
                	{
                		$status = 'died';
                	}
                	elseif($breeder->butchered == 1)
                	{
                		$status = 'butchered';
                	}
                	elseif(!empty($breeder->sold_at))
                	{
                		$status = 'sold';
                	}
                	else
                	{
                		$status = '';
                	}
                	return $status;
                }
                else if($key == 'status_date')
                {
                	if($breeder->archived == 1)
                	{
                		$statusDate = '';
                	}
                	elseif($breeder->died == 1)
                	{
                		$statusDate = Carbon::parse($breeder->died_at)->format('d/m/Y');
                	}
                	elseif($breeder->butchered == 1)
                	{
                		$statusDate = Carbon::parse($breeder->butchered_at)->format('d/m/Y');
                	}
                	elseif(!empty($breeder->sold_at))
                	{
                		$statusDate = Carbon::parse($breeder->sold_at)->format('d/m/Y');
                	}
                	else
                	{
                		$statusDate = '';
                	}
                	return $statusDate;
                }
                else
                {
                	return $breeder->$key;
                }
            }, $keys), ',');
        });
        rewind($fh);

        return stream_get_contents($fh);
    }
    
    public function exportLedgers($ledgers)
    {
    	$keys = [ 'debit', 'date', 'name', 'amount','description'];
    	$fh = fopen('php://memory', 'w+');
    	fputcsv($fh, ['Type', 'Date', 'Name', 'Amount', 'Notes'], ',');
    	$ledgers->each(function (LedgerEntry $ledger) use ($fh, $keys) {
    		fputcsv($fh, array_map(function ($key) use ($ledger) {
    			if($key == 'debit')
    			{
    				$val = $ledger->$key;
    				if($val == 1)
    				{
    					return 'Income';
    				}
    				else
    				{
    					return 'Expense';
    				}
    			}
    			return $ledger->$key;
    		}, $keys), ',');
    	});
    	rewind($fh);
    	
    	return stream_get_contents($fh);
    }
}
