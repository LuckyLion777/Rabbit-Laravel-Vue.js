<?php

namespace App\ImportExport;

use App\Contracts\ImportExport\EvansImporter as EvansImporterContract;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use App\Models\RabbitBreeder;
use Carbon\Carbon;
use phpQuery;
use Auth;
use Log;

class EvansImporter implements EvansImporterContract
{
	public function parseBreeders(UploadedFile $file)
	{
		$breeders = new Collection();;
		phpQuery::newDocumentFileHTML($file->path());
		$titleElement = pq('td.PedTitleFrame')->find('h1:eq(1)');
		$titleTxt = pq($titleElement)->text();
		$title = trim(str_replace('PEDIGREE', '', $titleTxt));
		$elements = [[], [], [], []];
		$trs = pq('tr');
		$cnt = 0;
		foreach ($trs as $tr)
		{
			$tds = pq($tr)->find('td');
			for($i = 0; $i < count($tds); $i++)
			{
				$td = $tds[$i];
				if(pq($td)->hasClass('male') || pq($td)->hasClass('female') || pq($td)->hasClass('neuter'))
				{
					if($cnt == 14)
					{
						$elements[0][] = $td;
					}
					else if($cnt == 6 || $cnt == 22)
					{
						$elements[1][] = $td;
					}
					else if($cnt == 2 || $cnt == 10 || $cnt == 18 || $cnt == 26)
					{
						$elements[2][] = $td;
					}
					else if($cnt == 0 || $cnt == 4 || $cnt == 8 || $cnt == 12 || $cnt == 16 || $cnt == 20 || $cnt == 24 || $cnt == 28)
					{
						$elements[3][] = $td;
					}
				}
			}
			$cnt++;
		}
		for($i = 0; $i < count($elements); $i++)
		{
			$elementGroup = $elements[$i];
			for($j = 0; $j < count($elementGroup); $j++)
			{
				$element = $elementGroup[$j];
				if(pq($element)->hasClass('male') || pq($element)->hasClass('female'))
				{
					$father = null;
					$mother = null;
					if($i < (count($elements) - 1))
					{
						$parents = [ $elements[$i+1][$j*2], $elements[$i+1][($j*2)+1] ];
						for ($k = 0; $k < 2; $k++)
						{
							$parent = $parents[$k];
							if(pq($parent)->hasClass('male'))
							{
								$father = $this->parseBreeder($title, $parent);
							}
							else if(pq($parent)->hasClass('female'))
							{
								$mother = $this->parseBreeder($title, $parent);
							}
						}
					}
					$breeder = $this->parseBreeder($title, $element);
					if(!empty($father))
					{
						$breeder->father_name = $father->name;
					}
					if(!empty($mother))
					{
						$breeder->mother_name = $mother->name;
					}
					$breeders->push($breeder);
				}
			}
		}
		$breeders = $breeders;
		return $breeders;
	}
	
	function parseBreeder($title, $element)
	{
		$breeder = new RabbitBreeder();
		$breeder->breed = $title;
		if(pq($element)->hasClass('male') == true)
		{
			$breeder->sex = 'buck';
		}
		else if(pq($element)->hasClass('female') == true)
		{
			$breeder->sex = 'doe';
		}
		$elementHtml = pq($element)->html();
		$lines = explode('<br>', $elementHtml);
		for ($i = 0; $i < count($lines); $i++)
		{
			$line = $lines[$i];
			if($i == 0)
			{
				$breeder->name = trim($line);
			}
			if($i == 1)
			{
				$breeder->color = trim($line);
			}
			if($i == 2)
			{
				$lineParts = explode('Reg #:', $line);
				if(count($lineParts) > 1)
				{
					$breeder->registration_number = trim($lineParts[1]);
				}
					
				$earLine = trim($lineParts[0]);
				$lineParts = explode('Ear #:', $earLine);
				if(count($lineParts) > 1)
				{
					$breeder->tattoo = trim($lineParts[1]);
				}
			}
			if($i == 3)
			{
				$lineParts = explode('Weight:', $line);
				if(count($lineParts) > 1)
				{
					$weightStr = trim($lineParts[1]);
					$weightStr = trim(str_replace('oz', '', $weightStr));
					$weightParts = explode('#', $weightStr);
					for($j = 0; $j < count($weightParts); $j++)
					{
						$weightParts[$j] = trim($weightParts[$j]);
					}
					$weight = empty($weightParts[1]) ? $weightParts[0] : implode('.', $weightParts);
					$weightUnit = Auth::user()->general_weight_units;
					$weightUnit = empty($weightUnit) ? 'Ounces' : $weightUnit;
					if($weightUnit == 'Ounces')
					{
						$weightParts = explode('.', $weight);
						if(count($weightParts) == 2)
						{
							$weight = intval($weightParts[1]) * 16 + intval($weightParts[0]);
						}
						else
						{
							$weight = intval($weightParts[0]);
						}
					}
					else if($weightUnit == 'Pounds')
					{
						$weightParts = explode('.', $weight);
						if(count($weightParts) == 2)
						{
							$weight = intval($weightParts[1]) * 16 + intval($weightParts[0]);
						}
						else
						{
							$weight = intval($weightParts[0]);
						}
						$weight = $weight/16;
					}
					else if($weightUnit == 'Pound/Ounces')
					{
						$weightParts = explode('.', $weight);
						if(count($weightParts) == 2)
						{
							$weight = intval($weightParts[1]) * 16 + intval($weightParts[0]);
						}
						else
						{
							$weight = intval($weightParts[0]);
						}
					}
					else if($weightUnit == 'Grams')
					{
						$weightParts = explode('.', $weight);
						if(count($weightParts) == 2)
						{
							$weight = intval($weightParts[1]) * 16 + intval($weightParts[0]);
						}
						else
						{
							$weight = intval($weightParts[0]);
						}
						$weight = $weight * 28.3495;
					}
					else if($weightUnit == 'Kilograms')
					{
						$weightParts = explode('.', $weight);
						if(count($weightParts) == 2)
						{
							$weight = intval($weightParts[1]) * 16 + intval($weightParts[0]);
						}
						else
						{
							$weight = intval($weightParts[0]);
						}
						$weight = ($weight * 28.3495) / 1000;
					}
					$breeder->weight = round($weight, 3);
				}
			}
			if($i == 4)
			{
				$lineParts = explode('Legs:', $line);
				if(count($lineParts) > 0)
				{
					$gcPart = trim($lineParts[0]);
					$gcParts = explode('GC:', $gcPart);
					if(count($gcParts) > 1)
					{
						$gcPart = trim($gcParts[1]);
						$breeder->champion_number = $gcPart;
					}
				}
				if(count($lineParts) > 1)
				{
					$legsPart = trim($lineParts[1]);
					$breeder->legs = $legsPart;
				}
			}
			if($i == 5)
			{
				$lineParts = explode('DOB:', $line);
				if(count($lineParts) > 1)
				{
					$dobStr = trim($lineParts[1]);
					$dobStr = str_replace("\xC2\xA0", '', $dobStr);
					if(!empty($dobStr))
					{
						if(Auth::user()->date_format == 'US')
						{
							$dobStr = Carbon::parse($dobStr)->format('m/d/Y');
						}
						else if(Auth::user()->date_format == 'INT')
						{
							$dobStr = Carbon::parse($dobStr)->format('d/m/Y');
						}
						else 
						{
							$dobStr = Carbon::parse($dobStr)->format('d/m/Y');
						}
					}
					$breeder->date_of_birth = $dobStr;
				}
			}
		}
		return $breeder;
	}
}
