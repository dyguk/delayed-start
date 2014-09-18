<?php

require_once 'startdelay.civix.php';

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function startdelay_civicrm_config(&$config) {
  _startdelay_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function startdelay_civicrm_xmlMenu(&$files) {
  _startdelay_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function startdelay_civicrm_install() {
  return _startdelay_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function startdelay_civicrm_uninstall() {
  return _startdelay_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function startdelay_civicrm_enable() {
  return _startdelay_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function startdelay_civicrm_disable() {
  return _startdelay_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function startdelay_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _startdelay_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function startdelay_civicrm_managed(&$entities) {
  return _startdelay_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function startdelay_civicrm_caseTypes(&$caseTypes) {
  _startdelay_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function startdelay_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _startdelay_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Hook implementation for altering membership types form.
 */
function startdelay_civicrm_buildForm($formName, &$form) {

  if ( is_a( $form, 'CRM_Member_Form_MembershipType' ) ) {

    // set the correct folder
    $folder     = DRUPAL_ROOT.'/sites/default/files/civicrm/custom_ext/nl.hhermsen.payment.startdelay';
    $settings_file  = $folder . '/settings.json';

    $settings = array();

    // read settings from file
    if(file_exists($settings_file)) {

      $settings = json_decode(file_get_contents($settings_file), true);
    }

    $membership_type_id = $form->getVar( '_id' );

    $attributes = array();

    if(array_key_exists($membership_type_id, $settings)) {

      $attributes['value'] = $settings[$membership_type_id];
    }

    // add the field element in the form
    $form->add('text', 'startdelay', ts('Start delay'), $attributes);

    // dynamically insert a template block in the page
    CRM_Core_Region::instance('page-body')->add(array(
      'template' => 'startdelay.tpl'
    ));

  }

  return;
}

/**
 * Hook implementation for saving membership types form start delay
 */
function startdelay_civicrm_postProcess( $formName, &$form ) {

  if ( is_a( $form, 'CRM_Member_Form_MembershipType' ) ) {

    // set the correct folder
    $folder     = DRUPAL_ROOT.'/sites/default/files/civicrm/custom_ext/nl.hhermsen.payment.startdelay';
    $settings_file  = $folder . '/settings.json';

    $settings = array();

    // read settings from file
    if(file_exists($settings_file)) {

      $settings = json_decode(file_get_contents($settings_file), true);
    }

    $membership_type_id = $form->getVar( '_id' );

    // get data from form
    $submitted = $form->controller->exportValues();
    $startdelay = abs((int) CRM_Utils_Array::value('startdelay', $submitted, 'NULL') );

    $settings[$membership_type_id] = $startdelay;

    file_put_contents($settings_file, json_encode($settings));

  }

  return;
}

/**
 * Hook implementation for altering payment parameters before talking to a payment processor back end.
 *
 * @param string $paymentObj
 *    instance of payment class of the payment processor invoked (e.g., 'CRM_Core_Payment_Dummy')
 * @param array &$rawParams
 *    array of params as passed to to the processor
 * @params array  &$cookedParams
 *     params after the processor code has translated them into its own key/value pairs
 * @return void
 */
function startdelay_civicrm_alterPaymentProcessorParams( $paymentObj,
                                                       &$rawParams,
                                                       &$cookedParams ) {

    // set the correct folder
    $folder     = DRUPAL_ROOT.'/sites/default/files/civicrm/custom_ext/nl.hhermsen.payment.startdelay';
    $settings_file  = $folder . '/settings.json';

    $settings = array();

    // read settings from file
    if(file_exists($settings_file)) {

      $settings = json_decode(file_get_contents($settings_file), true);
    }

    $membership_type_id = (int)$rawParams['selectMembership'];
    $contact_id = (int)$rawParams['contactID'];

    if(array_key_exists($membership_type_id, $settings) and (int)$settings[$membership_type_id] > 0) {

      $daysElapsedSinceFirstMembershipRecurrentPaymentProfile = CRM_Core_DAO::singleValueQuery("
        SELECT
          DATEDIFF(NOW(), b.start_date)
        FROM
          civicrm_membership a
        INNER JOIN
          civicrm_contribution_recur b
          ON a.contribution_recur_id = b.id
        WHERE
          a.contact_id = ".$contact_id."
          AND a.membership_type_id = ".$membership_type_id."
        ORDER BY
          b.start_date ASC
        LIMIT
          1
        ");

      if($settings[$membership_type_id] - $daysElapsedSinceFirstMembershipRecurrentPaymentProfile > 0) {

        $start_delay = $settings[$membership_type_id] - $daysElapsedSinceFirstMembershipRecurrentPaymentProfile;

        // do payment delay based on membership-type(-id) 
        $date = date_create();
        $date->add(new DateInterval('P'.$start_delay.'D'));
        $cookedParams['receive_date'] = $date->format('Y-m-d');
      }

    }

    return;
}

?>
