<?php
/**
 * CKEHelper is a helper for CKEEditor.
 * This helper REQUIRES the CKEEditor installation files.
 *
 * @package       cake
 * @subpackage    cake.app.plugins.wysiwyg.views.helpers
 * @author:       Jose Diaz-Gonzalez
 * @version:      1.1
 * @email:        support@savant.be
 * @site:         http://josediazgonzalez.com
 */
class CkHelper extends AppHelper {
/**
 * Helper dependencies
 *
 * @var array
 */
	var $helpers = array('Form', 'Html', 'Javascript');
/**
 * Whether helper has been initialized once or not
 *
 * @var boolean
 */
	var $_initialized = false;

/**
 * Holds the default model for the helper
 *
 * @var string
 */
	var $__defaultModel = null;

/**
 * Holds the model/field pair for the current element
 *
 * @var array
 */
	var $__modelFieldPair = null;

/**
 * Adds the cKeditor.js file and constructs the options
 *
 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
 * @param array $fckoptions Array of FckEditor attributes for this textarea
 * @return string JavaScript code to initialise the FckEditor area
 */
	function _build($field, $options = array()) {
		$modelFieldPair = $this->__field($field);
		$options = array_merge(
			array(
				'basepath' => $this->Html->base,
				'ckePath' => '/js/ckeditor/',
				'content' => '',
				'toolbarSet' => 'Default',
				'skinPath' => 'skins/',
				'skin' => 'default'),
			$options);
		if (!$this->_initialized) {
			// We don't want to add this every time, it's only needed once
			$this->_initialized = true;
			$this->Javascript->link('ckeditor/ckeditor.js', false);
		}

		/*
		return $this->Javascript->codeBlock(
			
		"window.onload = function() {};"
				var oCKeditor = new CKeditor('data[" . $modelFieldPair['model'] . "][" . $modelFieldPair['field'] . "]');
				oCKeditor.BasePath = \"" . $options['basepath'] . $options['ckePath'] . "\";
				oCKeditor.Config['SkinPath'] = \"" . $options['ckePath'] . $options['skinPath'] . $options['skin'] . '/' . "\";
				oCKeditor.Value = \"" . $options['content'] . "\";
				oCKeditor.ToolbarSet = \"" . $options['toolbarSet'] . "\";
				oCKeditor.ReplaceTextarea();
			}
			", array('safe' => false, 'inline' => false)
		);
		*/
	}

/**
* Creates an ckeditor input field
*
* @param string $field - used to build input name for views, 
* @param array $options Array of HTML attributes.
* @param array $fckOptions Array of FckEditor attributes for this input field
* @return string An HTML input element with FckEditor
*/
	function input($field = null, $options = array(), $ckOptions = array()){
		$buildId = (isset($options['id']))? $options['id']: $field;
		$js = $this->Javascript->codeBlock( "CKEDITOR.replace( '{$buildId}');");
		return $this->Form->input($field, $options) . $this->_build($buildId, $ckOptions). $js;
		
	}

/**
* Creates an fckeditor textarea
*
* @param string $field - used to build input name for views, 
* @param array $options Array of HTML attributes.
* @param array $ckOptions Array of CkEditor attributes for this textarea
* @return string An HTML textarea element with CkEditor
*/
	function textarea($field = null, $options = array(), $ckOptions = array()){
		$buildId = (isset($options['id']))? $options['id']: $field;
		$js = $this->Javascript->codeBlock( "CKEDITOR.replace( '{$buildId}');");
		return $this->Form->textarea($field, $options) . $this->_build($buildId, $ckOptions). $js;
	}

/**
 * Returns the modelname and fieldname for the current string
 *
 * @return mixed Array containing modelName and/or fieldName, null in all other cases
 * @author Jose Diaz-Gonzalez
 **/
	function __field($field = null) {
		if (empty($field)) {
			return null;
		}

		$firstToken = strtok($field, '.');
		$secondToken = strtok('.');
		if($secondToken === false) {
			$modelName = $this->__defaultModel();
			$fieldName = $firstToken;
		} else {
			$modelName = $firstToken;
			$fieldName = $secondToken;
		}
		$this->__modelFieldPair = array('model' => $modelName, 'field' => $fieldName);
		return $this->__modelFieldPair;
	}

/**
 * Gets the default model of the paged sets
 *
 * @return string Model name or the inflected controller name if the model isn't initialized. Null in all other cases
 */
	function __defaultModel() {
		if ($this->__defaultModel != null) {
			return $this->__defaultModel;
		} else {
			if (!empty($this->params['controller'])) {
				$this->__defaultModel = Inflector::classify($this->params['controller']);
			}
		}
		return $this->__defaultModel;
	}
}
?>