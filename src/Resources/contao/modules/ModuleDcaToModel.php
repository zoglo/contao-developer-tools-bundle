<?php
namespace Zoglo\ContaoDeveloperToolsBundle;

use Contao\BackendModule;
use Contao\BackendTemplate;
use Contao\Input;

/**
 * Back end module "dca to model".
 */
class ModuleDcaToModel extends BackendModule
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_dca_to_model';

	/**
	 * Generate the module
	 */
	protected function compile()
	{
        $arrDCA = array();

        foreach ($GLOBALS['BE_MOD'] as $value)
        {
            foreach ($value as $module)
            {
                if (array_key_exists('tables', $module))
                {
                    foreach ($module['tables'] as $tableName)
                    {
                        $arrDCA[] = $tableName;
                    }
                }
            }
        }

        $this->Template->dcalist = $arrDCA;

        $output = '';

	    if (Input::post('FORM_SUBMIT') == 'tl_dcatomodel')
        {
            $pluralName  =  Input::post('pluralName');
            $modelName   =  Input::post('modelName');
            $table       =  Input::post('dca');
            $author      =  Input::post('author');
            $authorUrl   =  Input::post('authorUrl');

            $this->Template->selectedOption = $table;

            $this->loadDataContainer($table);
            $objTemplate = new BackendTemplate('be_dca_model_output');

            $aliasExists = false;
            $cssIdExists = false;

            $arrFields = array();

            // Output
            foreach ($GLOBALS['TL_DCA'][$table]['fields'] as $fieldName => $config)
            {
                $arrFields[] = array
                (
                    'skipField'  => false,
                    'skipMethod' => $fieldName === 'id',
                    'name'       => $fieldName,
                    'type'       => $this->getFieldType($config)
                );

                if ($fieldName === 'alias')
                {
                    $aliasExists = true;
                }
                elseif ($fieldName === 'cssID')
                {
                    $cssIdExists = true;

                    $arrFields[] = array
                    (
                        'skipField'  => true,
                        'skipMethod' => false,
                        'name'       => 'space',
                        'type'       => 'string'
                    );
                }
            }

            $objTemplate->pluralName  = $pluralName;
            $objTemplate->aliasExists = $aliasExists;
            $objTemplate->cssIdExists = $cssIdExists;
            $objTemplate->modelName   = $modelName;
            $objTemplate->author      = $author;
            $objTemplate->authorUrl   = $authorUrl;
            $objTemplate->fields      = $arrFields;

            $output = $objTemplate->parse();
        }

        $this->Template->output = preg_replace('/^\s*\*/m', ' *', $output);
	}

	private function getFieldType($config)
    {
        if (!array_key_exists('sql', $config))
        {
            return 'string';
        }

        $sqlContent = $config['sql'];

        // Checking if sql is array (e.g. tl_content)
        if (is_array($sqlContent))
        {
            return $sqlContent['type'];
        }
        else
        {
            switch ($sqlContent)
            {
                case strpos($sqlContent, 'int'):
                    return 'integer';

                case strpos($sqlContent, 'char'):
                    return 'boolean';

                default:
                    return 'string ';
            }
        }
    }
}