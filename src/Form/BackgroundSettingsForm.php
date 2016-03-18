<?php

/**
 * @file
 * Contains \Drupal\background\Form\BackgroundMyForm.
 */

namespace Drupal\background\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;


/**
 * Provides a Background form.
 */
class BackgroundSettingsForm extends FormBase{

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'background_my_form';
  }

  /**
   * {@inheritdoc}
   */  
  public function buildForm(array $form, FormStateInterface $form_state) {
    $data = \Drupal::configFactory()->getEditable('background.settings')->get('background_data');
    $form = array(
      '#type' => 'fieldset',
      '#title' => t('Background settings'),
      '#description' => t("Your background, or 'wallpaper', is displayed in the back of your site design."),
      '#weight' => 1,
    );
    $form['path'] = array(
      '#type' => 'textfield',
      '#title' => t('Path to current background image'),
      '#default_value' => $data['path'],
      '#description' => t('The path to the image file  which is used as your custom background image.')
      );
    $form['color'] = array(
      '#type' => 'textfield',
      '#title' => t('Color'),
      '#description' => t("If no background, you can change your background color."),
      '#default_value' => $data['color'],
      '#size' => 10,
      );
    $form['position'] = array(
      '#type' => 'select',
      '#title' => t('Position'),
      '#description' => t("Positioning of background image."),
      '#options' => array(
        '' => '< select >',
        'top left' => 'top left',
        'top center' => 'top center',
        'top right' => 'top right',
        'center left' => 'center left',
        'center center' => 'center center',
        'center right' => 'center right',
        'bottom left' => 'bottom left',
        'bottom center' => 'bottom center',
        'bottom right' => 'bottom right'),
      '#default_value' => $data['position'],
      );
    $form['repeat'] = array(
      '#type' => 'select',
      '#title' => t('Repeat'),
      '#description' => t("Repeat settings for the background image."),
      '#options' => array(
        '' => '< select >',
        'repeat' => 'repeat',
        'repeat-x' => 'repeat-x',
        'repeat-y' => 'repeat-y',
        'no-repeat' => 'no-repeat'),
      '#default_value' => $data['repeat'],
      );
    $form['attachment'] = array(
      '#type' => 'select',
      '#title' => t('Attachment'),
      '#description' => t("Sets whether a background image is fixed or scrolls with the rest of he page."),
      '#options' => array(
        '' => '< select >',
        'fixed' => 'fixed',
        'scroll' => 'scroll'),
      '#default_value' => $data['attachment'],
      );
    $form['file'] = array(
      '#type' => 'file',
      '#title' => t('Image'),
      '#description' => t('Upload a file, allowed extensions: jpg, jpeg, png, gif'),
      );
    $form['check'] = array(
      '#type'           => 'checkbox',
      '#title'          => t('Implement On Admin Pages'),
      '#default_value'  =>  isset($data['check']) ? $data['check'] : FALSE,
      '#description'    => t('Background Image will be implement on admin pages.'),
    );
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => 'Save configuration',
    );
    $form['button'] = array(
      '#type' => 'submit',
      '#value' => 'Reset to defaults',
      '#title'          => t('Reset Background'),
      '#description'    => t('Custom Background image or colour will be reset to default background.'),
      '#submit' => array('::resetForm'),
    );
    $form['#attributes'] = array('enctype' => 'multipart/form-data');
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    // @todo
    /*$file = file_save_upload('file', array(
      // Validates file is really an image.
      'file_validate_is_image' => array(),
      // Validate extensions.
      'file_validate_extensions' => array('png gif jpg jpeg'),
      ));
      // If the file passed validation:
    if ($file) {
    // Move the file, into the Drupal file system
      if ($file = file_move($file, 'public://', FILE_EXISTS_REPLACE)) {
        $form_state['storage']['file'] = $file;
      }
      else {
        form_set_error('file', t('Failed to write the uploaded file to the site\'s file folder.'));
      }
    }
    else {
      $form_state['storage']['file'] ='';
    }*/
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = \Drupal::configFactory()->getEditable('background.settings');
    $values = $form_state->getValues();
    $color = $values['color'];
    $position = $values['position'];
    $repeat = $values['repeat'];
    $attachment = $values['attachment'];
    $check = $values['check'];
    $path = $values['path'];

    $validators = array('file_validate_extensions' => array('png gif jpg jpeg'));
    $fileObj = file_save_upload('file', $validators, "public://background/");
    if(!empty($fileObj[0])) { // check if the array is not empty
        $fileObj = reset($fileObj);
        $fileObj->save();
        $path = $fileObj->getFileUri();
    } 

    $data = array(
      'path' => $path,
      'color' => $color,
      'position' => $position,
      'repeat' => $repeat,
      'attachment' => $attachment,
      'check' => $check,
    );
    $config->set('background_data', $data)->save();
    drupal_set_message(t('The form has been submitted and the image/colour has been used as your current background image/colour.'));
  }

  /**
   * Reset form.
   */
  public function resetForm(array &$form, FormStateInterface $form_state) {
    $config = \Drupal::configFactory()->getEditable('background.settings');
    $config->set('background_data', NULL)->save();
  }

}