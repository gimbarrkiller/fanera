<?
require_once('crest.php');

function writeToLog($data, $title = '') {
    $log = "\n------------------------\n";
    $log .= date("Y.m.d G:i:s") . "\n";
    $log .= (strlen($title) > 0 ? $title : 'DEBUG') . "\n";
    $log .= print_r($data, 1);
    $log .= "\n------------------------\n";
    file_put_contents(getcwd() . '/hook.log', $log, FILE_APPEND);
    return true;
}

$defaults = array('name' => '', 'phone' => '', 'comments' => '', 'saved' => '');

if (array_key_exists('saved', $_REQUEST)) {
    $defaults = $_REQUEST;
    writeToLog($_REQUEST, 'contactForm Request');

    $resultFind = CRest::call('batch',
        array(
            'halt'=> 0,
            'cmd'=> array(
                'find_contact' => 'crm.duplicate.findbycomm?'
                    .http_build_query(array(
                        "entity_type" => "CONTACT",
                        "type" => "PHONE",
                        "values" => array($_REQUEST['phone'])
                    )),
                'get_contact' => 'crm.contact.get?'
                    .http_build_query(array(
                        "id" => '$result[find_contact][CONTACT][0]',
                    )),
                'get_company' => 'crm.company.get?'
                    .http_build_query(array(
                        "id" => '$result[get_contact][COMPANY_ID]',
                        "select" => array("*"),//, "COMMUNICATIONS"),
                    )),
            )
        )
    );
    writeToLog($resultFind, 'contactForm CONTACT Result');
    writeToLog($resultFind['result']['result']['find_contact']['CONTACT'][0], 'contactForm ID');

    $contactID = intval($resultFind['result']['result']['find_contact']['CONTACT'][0]);

    if ($contactID == 0)
    {
        // Контакт отсутствует в базе
        $resultDeal = CRest::call('batch',
            array(
                'halt'=> 0,
                'cmd'=> array(
                    'user_create' => 'crm.contact.add?'
                        .http_build_query(array(
                            "fields[NAME]" => $_REQUEST['name'],
                            "fields[OPENED]" => "Y",
                            "fields[ASSIGNED_BY_ID]" => 1,
                            "fields[TYPE_ID]" => "CLIENT",
                            "fields[PHONE]" => array(
                                array(
                                    'VALUE' => $_REQUEST['phone'],
                                    'VALUE_TYPE' => "WORK"
                                )
                            )
                        )),
                    'user_deal' => 'crm.deal.add?'
                        .http_build_query(array(
                            "fields[TITLE]" => $_REQUEST['name'],
                            "fields[ASSIGNED_BY_ID]" => 1,
                            "fields[STAGE_ID]" => "NEW",
                            "fields[OPENED]" => "Y",
                            "fields[COMMENTS]" => $_REQUEST['comments'],
                            "fields[CONTACT_ID]" => '$result[user_create][CONTACT][0]',
                        ))
                )
            )
        );

        writeToLog($resultDeal, 'contactForm DEAL Result');
    } else {
        // Контакт найден в базе
        $resultDeal = CRest::call('batch',
            array(
                'halt'=> 0,
                'cmd'=> array(
                    'user_deal' => 'crm.deal.add?'
                        .http_build_query(array(
                            "fields[TITLE]" => $_REQUEST['name'],
                            "fields[ASSIGNED_BY_ID]" => 1,
                            "fields[STAGE_ID]" => "NEW",
                            "fields[OPENED]" => "Y",
                            "fields[COMMENTS]" => $_REQUEST['comments'],
                            "fields[CONTACT_ID]" => $contactID,
                        ))
                )
            )
        );

        writeToLog($resultDeal, 'contactForm DEAL Result');
    }

} else {
    writeToLog("Empty", 'contactForm NoParams');
}
