<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CageCardTemplateRequest;
use App\Models\CageCardTemplate;
use App\Models\Litter;
use App\Models\RabbitBreeder;
use App\Models\User;
use Auth;
use File;
use Illuminate\Http\Request;
use Schema;
use Storage;
use View;

/**
 * Class AdminCageCardsController
 * @package App\Http\Controllers\Admin
 */
class AdminCageCardsController extends Controller
{
    /**
     * Get list of all cage cards templates.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index (Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $page = $request->get('page');
        $type = $request->get('type');
        $limit = 10;
        
        switch (true) {
            case !is_null($page):
                $templates = $user->cageCardTemplates()->orderBy('created_at', 'desc')
                    ->offset(($page - 1) * $limit)
                    ->limit($limit)
                    ->get()
                    ->toArray();
                $totalPages = ceil($user->cageCardTemplates->count()/$limit);
                
                $pagination = array('totalPages' => $totalPages);
                break;
            
            case !is_null($type):
                $templates = $user->cageCardTemplates()->orderBy('created_at', 'desc')
                    ->where('type', $type)
                    ->get()
                    ->toArray();
                $pagination = array();
                break;
            default:
                $templates = $user->cageCardTemplates()->orderBy('created_at', 'desc')
                    ->get()
                    ->toArray();
                $pagination = array();
        }
        
        
        
        return response()->json(array('templates' => $templates) + $pagination);
    }
    
    /**
     * Delete cage card template entity.
     *
     * @param $id
     */
    public function destroy($id)
    {
        /** @var User $user */
        $user = Auth::user();
        $template = CageCardTemplate::findOrFail($id);
        
        if ($user->cageCardTemplates->contains($template)) {
            $template->delete();
        }
    }
    
    /**
     * Copy cage card template entity.
     *
     * @param $id
     */
    public function copy($id)
    {
        /** @var User $user */
        $user = Auth::user();
        /** @var CageCardTemplate $template */
        $template = CageCardTemplate::findOrFail($id);
        
        if ($user->cageCardTemplates->contains($template)) {
            $clone = $template->replicate();
            $clone->name = $template->name.' copy';
            $clone->save();
        }
    }
    
    /**
     *  Get list of fields for templates dropdown.
     *
     * @return array
     */
    public function getFieldsList()
    {
        
        return response()->json([
            'breeder' => array_merge(
                array('blank', 'custom'),
                $this->getBreedersFields()
            ),
            'litter'  => array_merge(
                array('blank', 'custom'),
                $this->getLittersFields()
            )
        ]);
    }
    
    /**
     * Store cage card.
     *
     * @param CageCardTemplateRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CageCardTemplateRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $template = $user->cageCardTemplates()->create($request->all());
        
        return response()->json($template->toArray());
    }
    
    /**
     * Update cage card.
     *
     * @param CageCardTemplateRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CageCardTemplateRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $data = $request->all();
        $ret = array();
        
        if (isset($data['id'])) {
            $template = $user->cageCardTemplates()->where('id', $data['id'])->first();
            
            if ($template) {
                $template->update($data);
                $ret = $template->fresh();
            }
        }
        
        return response()->json($ret);
    }
    
    /**
     * Get first entities for cage card preview.
     */
    public function getFirstEntities()
    {
        /** @var User $user */
        $user = Auth::user();
        $firstBreeder = $user->breeders()
            ->where([
                array('archived', false),
                array('butchered', false),
            ])
            ->whereNull('sold_at')
            ->with('category', 'mother', 'father')
            ->first();
        $firstLitter = $user->litters()
            ->where([
                array('archived', false),
                array('butchered', false),
            ])
            ->with('mother', 'father', 'rabbitKits')
            ->first();
        
        return response()->json([
            'breeder' => !is_null($firstBreeder) ? $firstBreeder->toArray() : array(),
            'litter'  => !is_null($firstLitter) ? $firstLitter->toArray() : array(),
        ]);
    }
    
    /**
     * Get list of user's breeders.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBreeders(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $breedersBuilder = $user->breeders()
            ->where([
                array('archived', false),
                array('butchered', false),
            ])
            ->whereNull('sold_at')
            ->with('category', 'mother', 'father');
        $filter = $request->get('filter');
        
        switch (true) {
            case in_array($filter, array('doe', 'buck')):
                $breeders = $breedersBuilder
                    ->where('sex', $filter)
                    ->get();
                break;
            case str_contains($filter, 'categories'):
                $categoryId = $request->get('category');
                $breeders = $breedersBuilder
                    ->whereHas('category', function ($query) use ($categoryId) {
                        $query->where('id', $categoryId);
                    })->get();
                break;
            default:
                $breeders = $breedersBuilder->get();
        }
        
        return response()->json($breeders->toArray());
    }
    
    /**
     * Get list of user's litters.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLitters(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $litters = $user->litters()
            ->where([
                array('archived', false),
                array('butchered', false),
            ])
            ->with('rabbitKits')
            ->get();
        
        foreach ($litters as $litter) {
            $litter->father = $litter->parents()
                ->where('sex', '=', 'buck')
                ->first();
            $litter->mother = $litter->parents()
                ->where('sex', '=', 'doe')
                ->first();
        }
        
        return response()->json($litters->toArray());
    }
    
    /**
     * Print batch.
     *
     * @param Request $request
     *
     * @param string $tid  Template ID.
     * @param string $eids Entities IDs.
     *
     * @return \Illuminate\Http\Response
     */
    public function printBatch(Request $request, $tid, $eids)
    {
        /** @var User $user */
        $user = Auth::user();
        $eids = explode(',', $eids);
        $template = $user->cageCardTemplates()->where('id', $tid)->first();
        
        if ($template) {
            $type = $template->type;
            $size = $template->size;
            $orientation = $template->orientation;
            $hole = $template->hole;
            $entities = $type == 'breeder' ? RabbitBreeder::find($eids) : Litter::find($eids);
            
            // Set entities data map.
            $entitiesDataMap = array();
            
            foreach ($entities as $entity) {
                foreach ($template->fields as $index => $fieldName) {
                    $name = !in_array($fieldName, array('custom', 'blank')) ?
                        ucfirst(str_replace('_', ' ', $fieldName)).': ' :
                        '';
                    $value = '';
                    
                    switch (true) {
                        // Custom.
                        case str_contains($fieldName, 'custom'):
                            $name = '';
                            $value = substr($fieldName, 7);
                            break;
                        
                        // Blank.
                        case $fieldName == 'blank':
                            $value = '<p></p>';
                            break;
                        
                        // Table.
                        case $index == 'table':
                            $value = $fieldName;
                            $name = $index;
                            break;
                        
                        // Relation.
                        case str_contains($fieldName, '_id'):
                            $relationStrParts = explode('_id', $fieldName);
                            $relation = $relationStrParts[0];
                            $nameParts = explode('_id', $fieldName);
                            $decoratedName = $this->getDecoratedFieldName($type, $nameParts[0]);
                            $name = ucfirst($decoratedName).':';
                            
                            if (method_exists($entity, $relation) &&
                                $entity->getAttributeValue('name')) {
                                $value = $entity->getAttributeValue('name');
                            }
                            
                            break;
                        
                        default:
                            $value = $entity->getAttributeValue($fieldName);
                    }
                    
                    // Litters special fields.
                    if ($type == 'litter') {
                        // Mother and father.
                        if (in_array($fieldName, array('mother', 'father'))) {
                            if (!is_null($entity->{$fieldName}->first())) {
                                $value = $entity->{$fieldName}->first()->name;
                            }
                        }
                        
                        // Given id.
                        if ($fieldName == 'given_id') {
                            $value = $entity->getAttributeValue($fieldName);
                        }
                    }
                    
                    if ($value) {
                        $entitiesDataMap[$entity->getKey()]['fields'][$index] = [
                            'name'  => $name,
                            'value' => $value,
                        ];
                    }
                    
                    // Kits.
                    if ($entity instanceof Litter) {
                        $entitiesDataMap[$entity->getKey()]['kits'] = $entity->kits()->toArray();
                    }
                    
                    // Profile url.
                    $entitiesDataMap[$entity->getKey()]['url'] = $entity instanceof RabbitBreeder ?
                        url('/#!/profile', [$entity->getKey()]) : url('/#!/litterprofile', [$entity->getKey()]);
                }
            }
            
            // Creating pdf.
            $viewName = sprintf(
                'layouts.cage-cards.partials.print.%s.%s.%s',
                $type,
                $orientation,
                $size
            );


            if($template->size == '4_7_large') {
                $viewName = sprintf(
                    'layouts.cage-cards.partials.print.%s.vertical.%s',
                    $type,
                    $size
                );
            }

	
			$paperFormatsMap = array(
                '2_3_business' => array(
                    'vertical' => array(
                        'size'        => array(0, 0, 144, 252),
                        'orientation' => 'portrait'
                    ),
                    'horizontal' => array(
                        'size'        => array(0, 0, 144, 252),
                        'orientation' => 'landscape'
                    )
                ),
                '3_5_index'    => array(
                    'vertical' => array(
                        'size' => array(0, 0, 216, 360),
                        'orientation' => 'portrait'
                    ),
                    'horizontal' => array(
                        'size' => array(0, 0, 216, 360),
                        'orientation' => 'landscape'
                    )
                ),
                '4_7_large'    => array(
                    'vertical' => array(
                        'size' => array(0, 0, 288, 504),
                        'orientation' => 'landscape'
                    ),
                    'horizontal' => array(
                        'size' => array(0, 0, 288, 504),
                        'orientation' => 'landscape'
                    )
                ),
            );
            
            $view = View::make($viewName, compact('entitiesDataMap', 'profileUrl', 'hole'));
            $pdf = \PDF::loadHTML($view->render())
                ->setPaper($paperFormatsMap[$template->size][$template->orientation]['size'], $paperFormatsMap[$template->size][$template->orientation]['orientation']);
            
            $filename = sprintf(
                '%s_%s_%s_%s_%s.pdf',
                Auth::id(),
                $type,
                $orientation,
                $size,
                date('Y_m_d_H_i_s')
            );
            
            // Huck to resolve https://github.com/dompdf/dompdf/issues/1272.
            return @$pdf->download($filename);
        }
    }
	
	
	/**
     * Print batch HTML.
     *
     * @param Request $request
     *
     * @param string $tid  Template ID.
     * @param string $eids Entities IDs.
     *
     * @return \Illuminate\Http\Response
     */
    public function printBatchHTML(Request $request, $tid, $eids)
    {
        /** @var User $user */
        $user = Auth::user();
        $eids = explode(',', $eids);
        $template = $user->cageCardTemplates()->where('id', $tid)->first();
        
        if ($template) {
            $type = $template->type;
            $size = $template->size;
            $orientation = $template->orientation;
            $hole = $template->hole;
            $entities = $type == 'breeder' ? RabbitBreeder::find($eids) : Litter::find($eids);
            
            // Set entities data map.
            $entitiesDataMap = array();
            
            foreach ($entities as $entity) {
                foreach ($template->fields as $index => $fieldName) {
                    $name = !in_array($fieldName, array('custom', 'blank')) ?
                        ucfirst(str_replace('_', ' ', $fieldName)).': ' :
                        '';
                    $value = '';
                    
                    switch (true) {
                        // Custom.
                        case str_contains($fieldName, 'custom'):
                            $name = '';
                            $value = substr($fieldName, 7);
                            break;
                        
                        // Blank.
                        case $fieldName == 'blank':
                            $value = '<p></p>';
                            break;
                        
                        // Table.
                        case $index == 'table':
                            $value = $fieldName;
                            $name = $index;
                            break;
                        
                        // Relation.
                        case str_contains($fieldName, '_id'):
                            $relationStrParts = explode('_id', $fieldName);
                            $relation = $relationStrParts[0];
                            $nameParts = explode('_id', $fieldName);
                            $decoratedName = $this->getDecoratedFieldName($type, $nameParts[0]);
                            $name = ucfirst($decoratedName).':';
                            
                            if (method_exists($entity, $relation) &&
                                $entity->getAttributeValue('name')) {
                                $value = $entity->getAttributeValue('name');
                            }
                            
                            break;
                        
                        default:
                            $value = $entity->getAttributeValue($fieldName);
                    }
                    
                    // Litters special fields.
                    if ($type == 'litter') {
                        // Mother and father.
                        if (in_array($fieldName, array('mother', 'father'))) {
                            if (!is_null($entity->{$fieldName}->first())) {
                                $value = $entity->{$fieldName}->first()->name;
                            }
                        }
                        
                        // Given id.
                        if ($fieldName == 'given_id') {
                            $value = $entity->getAttributeValue($fieldName);
                        }
                    }
                    
                    if ($value) {
                        $entitiesDataMap[$entity->getKey()]['fields'][$index] = [
                            'name'  => $name,
                            'value' => $value,
                        ];
                    }
                    
                    // Kits.
                    if ($entity instanceof Litter) {
                        $entitiesDataMap[$entity->getKey()]['kits'] = $entity->kits()->toArray();
                    }
                    
                    // Profile url.
                    $entitiesDataMap[$entity->getKey()]['url'] = $entity instanceof RabbitBreeder ?
                        url('/#!/profile', [$entity->getKey()]) : url('/#!/litterprofile', [$entity->getKey()]);
                }
            }
            
            // Creating pdf.
            $viewName = sprintf(
                'layouts.cage-cards.partials.print.%s.%s.%s',
                $type,
                $orientation,
                $size
            );


            if($template->size == '4_7_large') {
                $viewName = sprintf(
                    'layouts.cage-cards.partials.print.%s.vertical.%s',
                    $type,
                    $size
                );
            }
		
			$paperFormatsMap = array(
                '2_3_business' => array(
                    'vertical' => array(
                        'size'        => array(0, 0, 144, 252),
                        'orientation' => 'portrait'
                    ),
                    'horizontal' => array(
                        'size'        => array(0, 0, 144, 252),
                        'orientation' => 'landscape'
                    )
                ),
                '3_5_index'    => array(
                    'vertical' => array(
                        'size' => array(0, 0, 216, 360),
                        'orientation' => 'portrait'
                    ),
                    'horizontal' => array(
                        'size' => array(0, 0, 216, 360),
                        'orientation' => 'landscape'
                    )
                ),
                '4_7_large'    => array(
                    'vertical' => array(
                        'size' => array(0, 0, 288, 504),
                        'orientation' => 'landscape'
                    ),
                    'horizontal' => array(
                        'size' => array(0, 0, 288, 504),
                        'orientation' => 'landscape'
                    )
                ),
            );
            
            
            $view = View::make($viewName, compact('entitiesDataMap', 'profileUrl', 'hole'));
			echo $view;
            /**
			$pdf = \PDF::loadHTML($view->render())
               ->setPaper($paperFormatsMap[$template->size][$template->orientation]['size'], $paperFormatsMap[$template->size][$template->orientation]['orientation']);
            
            $filename = sprintf(
                '%s_%s_%s_%s_%s.pdf',
                Auth::id(),
                $type,
                $orientation,
                $size,
                date('Y_m_d_H_i_s')
            );
            
            // Huck to resolve https://github.com/dompdf/dompdf/issues/1272.
            return @$pdf->download($filename);
			*/
        }
    }
	
	
    
    /**
     * Get breeders fields.
     *
     * @return array
     */
    public function getBreedersFields()
    {
        $fields = Schema::getColumnListing('rabbit_breeders');
        
        $removedFields = array(
            'id',
            'image',
            'notes',
            'archived',
            'died',
            'died_at',
            'butchered',
            'butchered_at',
            'sold_at',
            'user_id',
            'litters_count',
            'live_kits',
            'survival_rate',
            'created_at',
            'updated_at',
            'death_reason',
        );
        
        foreach ($removedFields as $field) {
            if(($key = array_search($field, $fields)) !== false) {
                unset($fields[$key]);
            }
        }
        
        return $fields;
    }
    
    /**
     * Get litters fields.
     *
     * @return array
     */
    public function getLittersFields()
    {
        $fields = Schema::getColumnListing('litters');
        
        $removedFields = array(
            'id',
            'notes',
            'user_id',
            'archived',
            'created',
            'updated',
            'butchered',
            'died',
            'user',
            'archived_at',
            'created_at',
            'updated_at',
            'butchered_at'
        );
        
        foreach ($removedFields as $field) {
            if(($key = array_search($field, $fields)) !== false) {
                unset($fields[$key]);
            }
        }
        
        $fields = array_merge($fields, array('mother', 'father'));
        
        return $fields;
    }
    
    /**
     * Decorate field name.
     *
     * @param $type
     * @param $fieldName
     *
     * @return string
     */
    public function getDecoratedFieldName($type, $fieldName)
    {
        $ret = $fieldName;
        $map = $type == 'breeder' ? array(
            'tattoo'  => 'id',
            'aquired' => 'acquired',
        ) : array('given' => 'id');
        
        foreach ($map as $original => $decorated) {
            if($fieldName == $original) {
                $ret = $decorated;
            }
        }
        
        return $ret;
    }
}