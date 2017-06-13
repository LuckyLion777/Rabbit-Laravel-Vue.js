<?php

namespace App\ImportExport;

use App\Contracts\ImportExport\CsvImporter as CsvImporterContract;
use App\Models\RabbitBreeder;
use App\Models\Ledger\Entry as LedgerEntry;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class CsvImporter implements CsvImporterContract
{
    public function parseBreeders(UploadedFile $file)
    {
        $collection = new Collection();

        $keys = ['sex', 'name', 'tattoo', 'cage', 'color', 'breed', 'weight', 'date_of_birth',
                 'aquired', 'registration_number', 'champion_number', 'legs', 'status', 'status_date'];

        $fileObject = $file->openFile();
        $fileObject->setFlags(\SplFileObject::READ_CSV | \SplFileObject::DROP_NEW_LINE | \SplFileObject::SKIP_EMPTY);
        $fileObject->setCsvControl(',');
        foreach ($fileObject as $row) {
            if (!$row) continue;
            if (strtolower($row[0]) === 'sex') continue;
            $breeder = new RabbitBreeder();
            $keyValArr = array_combine($keys, $row);
            foreach ($keyValArr as $key => $value) {
            	if($key == 'status')
            	{
            		if($value == 'archived')
            		{
            			$breeder->archived = 1;
            		}
            		elseif($value == 'died')
            		{
            			$breeder->died = 1;
            		}
            		elseif($value == 'butchered')
            		{
            			$breeder->butchered = 1;
            		}
            	}
            	elseif($key == 'status_date')
            	{
            		$status = $keyValArr['status'];
            		
            		$dateStr = $value;
            		$dateStrParts = preg_split("/[\/\-]/", $dateStr);
            		if(strlen($dateStrParts[count($dateStrParts) - 1]) == 2)
            		{
            			$dateStrParts[count($dateStrParts) - 1] = '20'.$dateStrParts[count($dateStrParts) - 1];
            		}
            		$value = implode('/', $dateStrParts);
            		
            		if($status == 'died')
            		{
            			$breeder->died_at = $value;
            		}
            		else if($status == 'butchered')
            		{
            			$breeder->butchered_at = $value;
            		}
            		else if($status == 'sold')
            		{
            			$breeder->sold_at = $value;
            		}
            	}
            	elseif($key == 'date_of_birth')
            	{
            		$dateStr = $value;
            		$dateStrParts = preg_split("/[\/\-]/", $dateStr);
            		if(strlen($dateStrParts[count($dateStrParts) - 1]) == 2)
            		{
            			$dateStrParts[count($dateStrParts) - 1] = '20'.$dateStrParts[count($dateStrParts) - 1];
            		}
            		$value = implode('/', $dateStrParts);
            	}
            	elseif ($key == 'aquired')
            	{
            		$dateStr = $value;
            		$dateStrParts = preg_split("/[\/\-]/", $dateStr);
            		if(strlen($dateStrParts[count($dateStrParts) - 1]) == 2)
            		{
            			$dateStrParts[count($dateStrParts) - 1] = '20'.$dateStrParts[count($dateStrParts) - 1];
            		}
            		$value = implode('/', $dateStrParts);
            	}
            	$breeder->$key = $value;
            }
            $collection->push($breeder);
        }

        return $collection;
    }
    
    public function parseLedgers(UploadedFile $file)
    {
    	$collection = new Collection();
    	$keys = ['debit', 'date', 'name', 'amount','description'];
    	$fileObject = $file->openFile();
    	$fileObject->setFlags(\SplFileObject::READ_CSV | \SplFileObject::DROP_NEW_LINE | \SplFileObject::SKIP_EMPTY);
    	$fileObject->setCsvControl(',');
    	foreach ($fileObject as $row) {
    		if (!$row) continue;
    		if (strtolower($row[0]) === 'type') continue;
    		$ledger = new LedgerEntry();
    		foreach (array_combine($keys, $row) as $key => $value) {
    			if($key == 'debit')
    			{
    				if($value == 'Income')
    				{
    					$ledger->$key = '1';
    				}
    				else
    				{
    					$ledger->$key = '0';
    				}
    			}
    			elseif($key == 'date')
    			{
    				$dateStr = $value;
    				$dateStrParts = preg_split("/[\/\-]/", $dateStr);
    				if(strlen($dateStrParts[count($dateStrParts) - 1]) == 2)
    				{
    					$dateStrParts[count($dateStrParts) - 1] = '20'.$dateStrParts[count($dateStrParts) - 1];
    				}
    				$dateStr = implode('/', $dateStrParts);
    				$ledger->date = $dateStr;
    			}
    			else
    			{
    				$ledger->$key = $value;
    			}
    		}
    		$collection->push($ledger);
    	}
    	return $collection;
    }
}
