<?php

$iOldQID = (int) $insertdata['qid'];
unset($insertdata['qid']);
$insertdata['parent_qid'] = $aQIDReplacements[(int) $insertdata['parent_qid']];
if (!isset($insertdata['help'])) {
    $insertdata['help'] = '';
}
if (!isset($xml->question_l10ns->rows->row)) {
    // Edit difference here.
    if ($options['translinkfields']) {
        $insertdata['question'] = translateLinks('survey', $iOldSID, $iNewSID, $insertdata['question']);
        $insertdata['help'] = translateLinks('survey', $iOldSID, $iNewSID, $insertdata['help']);
    }
    $oQuestionL10n = new QuestionL10n();
    $oQuestionL10n->question = $insertdata['question'];
    $oQuestionL10n->help = $insertdata['help'];
    $oQuestionL10n->language = $insertdata['language'];
    unset($insertdata['question']);
    unset($insertdata['help']);
    unset($insertdata['language']);
}

foo();
