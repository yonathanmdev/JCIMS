<?php
use App\Helpers\EthiopianDateHelper; 

$report = $report1 ?? [
    'urban_m_parents' => 0, 'urban_f_parents' => 0, 'rural_m_parents' => 0, 'rural_f_parents' => 0,
    'urban_m_others' => 0, 'urban_f_others' => 0, 'rural_m_others' => 0, 'rural_f_others' => 0,
    'urban_m_advice' => 0, 'urban_f_advice' => 0, 'rural_m_advice' => 0, 'rural_f_advice' => 0,
    'urban_m_age15_29' => 0, 'urban_f_age15_29' => 0, 'rural_m_age15_29' => 0, 'rural_f_age15_29' => 0,
    'urban_m_age30_64' => 0, 'urban_f_age30_64' => 0, 'rural_m_age30_64' => 0, 'rural_f_age30_64' => 0,
    'urban_m_uni' => 0, 'urban_f_uni' => 0, 'rural_m_uni' => 0, 'rural_f_uni' => 0,
    'urban_m_tvt' => 0, 'urban_f_tvt' => 0, 'rural_m_tvt' => 0, 'rural_f_tvt' => 0,
    'urban_m_phy' => 0, 'urban_f_phy' => 0, 'rural_m_phy' => 0, 'rural_f_phy' => 0,
    'urban_m_immg' => 0, 'urban_f_immg' => 0, 'rural_m_immg' => 0, 'rural_f_immg' => 0,
    'urban_m_teff' => 0, 'urban_f_teff' => 0, 'rural_m_teff' => 0, 'rural_f_teff' => 0,
    'urban_m_noh' => 0, 'urban_f_noh' => 0, 'rural_m_noh' => 0, 'rural_f_noh' => 0,
    'urban_m_ajs' => 0, 'urban_f_ajs' => 0, 'rural_m_ajs' => 0, 'rural_f_ajs' => 0,
    'urban_m_ajs15_29' => 0, 'urban_f_ajs15_29' => 0, 'rural_m_ajs15_29' => 0, 'rural_f_ajs15_29' => 0,
    'urban_m_ajsuni' => 0, 'urban_f_ajsuni' => 0, 'rural_m_ajsuni' => 0, 'rural_f_ajsuni' => 0,
    'urban_m_ajstvt' => 0, 'urban_f_ajstvt' => 0, 'rural_m_ajstvt' => 0, 'rural_f_ajstvt' => 0,
    'urban_m_ajsdis' => 0, 'urban_f_ajsdis' => 0, 'rural_m_ajsdis' => 0, 'rural_f_ajsdis' => 0,
    'urban_m_ajsimmg' => 0, 'urban_f_ajsimmg' => 0, 'rural_m_ajsimmg' => 0, 'rural_f_ajsimmg' => 0,
    'urban_m_ajsteff' => 0, 'urban_f_ajsteff' => 0, 'rural_m_ajsteff' => 0, 'rural_f_ajsteff' => 0,
    'urban_m_ajsnoh' => 0, 'urban_f_ajsnoh' => 0, 'rural_m_ajsnoh' => 0, 'rural_f_ajsnoh' => 0,
    'urban_f_ajcufp' => 0, 'urban_f_ajcuft' => 0, 'rural_f_ajcrfp' => 0, 'rural_f_ajcrft' => 0,
    'rural_m_ajc15_29p' => 0, 'rural_f_ajc15_29p' => 0, 'rural_m_ajc15_29t' => 0, 'rural_f_ajc15_29t' => 0,'urban_m_ajc15_29p'=> 0, 'urban_f_ajc15_29p' => 0, 'urban_m_ajc15_29t' => 0, 'urban_f_ajc15_29t' => 0,
    'rural_m_ajcphydisp' => 0, 'rural_f_ajcphydisp' => 0, 'rural_m_ajcphydist' => 0, 'rural_f_ajcphydist' => 0,'urban_m_ajcphydisp'=> 0, 'urban_f_ajcphydisp' => 0, 'urban_m_ajcphydist' => 0, 'urban_f_ajcphydist' => 0,
    'rural_m_ajcimmgp' => 0, 'rural_f_ajcimmgp' => 0, 'rural_m_ajcimmgt' => 0, 'rural_f_ajcimmgt' => 0,'urban_m_ajcimmgp'=> 0, 'urban_f_ajcimmgp' => 0, 'urban_m_ajcimmgt' => 0, 'urban_f_ajcimmgt' => 0,
    'rural_m_ajcteffp' => 0, 'rural_f_ajcteffp' => 0, 'rural_m_ajctefft' => 0, 'rural_f_ajctefft' => 0,'urban_m_ajcteffp'=> 0, 'urban_f_ajcteffp' => 0, 'urban_m_ajctefft' => 0, 'urban_f_ajctefft' => 0,
    'rural_m_ajcnohp' => 0, 'rural_f_ajcnohp' => 0, 'rural_m_ajcnoht' => 0, 'rural_f_ajcnoht' => 0,'urban_m_ajcnohp'=> 0, 'urban_f_ajcnohp' => 0, 'urban_m_ajcnoht' => 0, 'urban_f_ajcnoht' => 0,
    'rural_m_ajcunip' => 0, 'rural_f_ajcunip' => 0, 'rural_m_ajcunit' => 0, 'rural_f_ajcunit' => 0,'urban_m_ajcunip'=> 0, 'urban_f_ajcunip' => 0, 'urban_m_ajcunit' => 0, 'urban_f_ajcunit' => 0,
    'rural_m_ajctvtp' => 0, 'rural_f_ajctvtp' => 0, 'rural_m_ajctvtt' => 0, 'rural_f_ajctvtt' => 0,'urban_m_ajctvtp'=> 0, 'urban_f_ajctvtp' => 0, 'urban_m_ajctvtt' => 0, 'urban_f_ajctvtt' => 0
   
    ];

$selectedBranchName = $branchData['name'] ?? $_SESSION['user']['branch_name'];

// Calculations
$totalUrbanparents = $report['urban_m_parents'] + $report['urban_f_parents'];
$totalRuralparents = $report['rural_m_parents'] + $report['rural_f_parents'];
$totalMaleparents  = $report['urban_m_parents'] + $report['rural_m_parents'];
$totalFemaleparents = $report['urban_f_parents'] + $report['rural_f_parents'];
$grandTotalparents = $totalUrbanparents + $totalRuralparents;

$totalUrbanothers = $report['urban_m_others'] + $report['urban_f_others'];
$totalRuralothers = $report['rural_m_others'] + $report['rural_f_others'];
$totalMaleothers  = $report['urban_m_others'] + $report['rural_m_others'];
$totalFemaleothers = $report['urban_f_others'] + $report['rural_f_others'];
$grandTotalothers = $totalUrbanothers + $totalRuralothers;

$totalUrbanadvice = $report['urban_m_advice'] + $report['urban_f_advice'];
$totalRuraladvice = $report['rural_m_advice'] + $report['rural_f_advice'];
$totalMaleadvice  = $report['urban_m_advice'] + $report['rural_m_advice'];
$totalFemaleadvice = $report['urban_f_advice'] + $report['rural_f_advice'];
$grandTotaladvice = $totalUrbanadvice + $totalRuraladvice;

$totalUrbanage15_29 = $report['urban_m_age15_29'] + $report['urban_f_age15_29'];
$totalRuralage15_29 = $report['rural_m_age15_29'] + $report['rural_f_age15_29'];
$totalMaleage15_29  = $report['urban_m_age15_29'] + $report['rural_m_age15_29'];
$totalFemaleage15_29 = $report['urban_f_age15_29'] + $report['rural_f_age15_29'];
$grandTotalage15_29 = $totalUrbanage15_29 + $totalRuralage15_29;

$totalUrbanage30_64 = $report['urban_m_age30_64'] + $report['urban_f_age30_64'];
$totalRuralage30_64 = $report['rural_m_age30_64'] + $report['rural_f_age30_64'];
$totalMaleage30_64  = $report['urban_m_age30_64'] + $report['rural_m_age30_64'];
$totalFemaleage30_64 = $report['urban_f_age30_64'] + $report['rural_f_age30_64'];
$grandTotalage30_64 = $totalUrbanage30_64 + $totalRuralage30_64;

$totalUrbanuni = $report['urban_m_uni'] + $report['urban_f_uni'];
$totalRuraluni = $report['rural_m_uni'] + $report['rural_f_uni'];
$totalMaleuni  = $report['urban_m_uni'] + $report['rural_m_uni'];
$totalFemaleuni = $report['urban_f_uni'] + $report['rural_f_uni'];
$grandTotaluni = $totalUrbanuni + $totalRuraluni;

$totalUrbantvt = $report['urban_m_tvt'] + $report['urban_f_tvt'];
$totalRuraltvt = $report['rural_m_tvt'] + $report['rural_f_tvt'];
$totalMaletvt  = $report['urban_m_tvt'] + $report['rural_m_tvt'];
$totalFemaletvt = $report['urban_f_tvt'] + $report['rural_f_tvt'];
$grandTotaltvt = $totalUrbantvt + $totalRuraltvt;

$totalUrbanphy = $report['urban_m_phy'] + $report['urban_f_phy'];
$totalRuralphy = $report['rural_m_phy'] + $report['rural_f_phy'];
$totalMalephy  = $report['urban_m_phy'] + $report['rural_m_phy'];
$totalFemalephy = $report['urban_f_phy'] + $report['rural_f_phy'];
$grandTotalphy = $totalUrbanphy + $totalRuralphy;

$totalUrbanimmg = $report['urban_m_immg'] + $report['urban_f_immg'];
$totalRuralimmg = $report['rural_m_immg'] + $report['rural_f_immg'];
$totalMaleimmg  = $report['urban_m_immg'] + $report['rural_m_immg'];
$totalFemaleimmg = $report['urban_f_immg'] + $report['rural_f_immg'];
$grandTotalimmg = $totalUrbanimmg + $totalRuralimmg;

$totalUrbanteff = $report['urban_m_teff'] + $report['urban_f_teff'];
$totalRuralteff = $report['rural_m_teff'] + $report['rural_f_teff'];
$totalMaleteff  = $report['urban_m_teff'] + $report['rural_m_teff'];
$totalFemaleteff = $report['urban_f_teff'] + $report['rural_f_teff'];
$grandTotalteff = $totalUrbanteff + $totalRuralteff;

$totalUrbannoh = $report['urban_m_noh'] + $report['urban_f_noh'];
$totalRuralnoh = $report['rural_m_noh'] + $report['rural_f_noh'];
$totalMalenoh  = $report['urban_m_noh'] + $report['rural_m_noh'];
$totalFemalenoh = $report['urban_f_noh'] + $report['rural_f_noh'];
$grandTotalnoh = $totalUrbannoh + $totalRuralnoh;

$totalUrbanajs = $report['urban_m_ajs'] + $report['urban_f_ajs'];
$totalRuralajs = $report['rural_m_ajs'] + $report['rural_f_ajs'];
$totalMaleajs  = $report['urban_m_ajs'] + $report['rural_m_ajs'];
$totalFemaleajs = $report['urban_f_ajs'] + $report['rural_f_ajs'];
$grandTotalajs = $totalUrbanajs + $totalRuralajs;

$totalUrbanajs15_29 = $report['urban_m_ajs15_29'] + $report['urban_f_ajs15_29'];
$totalRuralajs15_29 = $report['rural_m_ajs15_29'] + $report['rural_f_ajs15_29'];
$totalMaleajs15_29  = $report['urban_m_ajs15_29'] + $report['rural_m_ajs15_29'];
$totalFemaleajs15_29 = $report['urban_f_ajs15_29'] + $report['rural_f_ajs15_29'];
$grandTotalajs15_29 = $totalUrbanajs15_29 + $totalRuralajs15_29;

$totalUrbanajsuni = $report['urban_m_ajsuni'] + $report['urban_f_ajsuni'];
$totalRuralajsuni = $report['rural_m_ajsuni'] + $report['rural_f_ajsuni'];
$totalMaleajsuni  = $report['urban_m_ajsuni'] + $report['rural_m_ajsuni'];
$totalFemaleajsuni = $report['urban_f_ajsuni'] + $report['rural_f_ajsuni'];
$grandTotalajsuni = $totalUrbanajsuni + $totalRuralajsuni;

$totalUrbanajstvt = $report['urban_m_ajstvt'] + $report['urban_f_ajstvt'];
$totalRuralajstvt = $report['rural_m_ajstvt'] + $report['rural_f_ajstvt'];
$totalMaleajstvt  = $report['urban_m_ajstvt'] + $report['rural_m_ajstvt'];
$totalFemaleajstvt = $report['urban_f_ajstvt'] + $report['rural_f_ajstvt'];
$grandTotalajstvt = $totalUrbanajstvt + $totalRuralajstvt;

$totalUrbanajsdis = $report['urban_m_ajsdis'] + $report['urban_f_ajsdis'];
$totalRuralajsdis = $report['rural_m_ajsdis'] + $report['rural_f_ajsdis'];
$totalMaleajsdis  = $report['urban_m_ajsdis'] + $report['rural_m_ajsdis'];
$totalFemaleajsdis = $report['urban_f_ajsdis'] + $report['rural_f_ajsdis'];
$grandTotalajsdis = $totalUrbanajsdis + $totalRuralajsdis;

$totalUrbanajsimmg = $report['urban_m_ajsimmg'] + $report['urban_f_ajsimmg'];
$totalRuralajsimmg = $report['rural_m_ajsimmg'] + $report['rural_f_ajsimmg'];
$totalMaleajsimmg  = $report['urban_m_ajsimmg'] + $report['rural_m_ajsimmg'];
$totalFemaleajsimmg = $report['urban_f_ajsimmg'] + $report['rural_f_ajsimmg'];
$grandTotalajsimmg = $totalUrbanajsimmg + $totalRuralajsimmg;

$totalUrbanajsteff = $report['urban_m_ajsteff'] + $report['urban_f_ajsteff'];
$totalRuralajsteff = $report['rural_m_ajsteff'] + $report['rural_f_ajsteff'];
$totalMaleajsteff  = $report['urban_m_ajsteff'] + $report['rural_m_ajsteff'];
$totalFemaleajsteff = $report['urban_f_ajsteff'] + $report['rural_f_ajsteff'];
$grandTotalajsteff = $totalUrbanajsteff + $totalRuralajsteff;

$totalUrbanajsnoh = $report['urban_m_ajsnoh'] + $report['urban_f_ajsnoh'];
$totalRuralajsnoh = $report['rural_m_ajsnoh'] + $report['rural_f_ajsnoh'];
$totalMaleajsnoh  = $report['urban_m_ajsnoh'] + $report['rural_m_ajsnoh'];
$totalFemaleajsnoh = $report['urban_f_ajsnoh'] + $report['rural_f_ajsnoh'];
$grandTotalajsnoh = $totalUrbanajsnoh + $totalRuralajsnoh;

$totalurban_f_ajcufp = $report['urban_f_ajcufp'];
$totalrural_f_ajcrfp = $report['rural_f_ajcrfp'];
$totalurban_f_ajcuft  = $report['urban_f_ajcuft'];
$totalrural_f_ajcrft = $report['rural_f_ajcrft'];

$Totalajcu = $totalurban_f_ajcufp + $totalurban_f_ajcuft;
$Totalajcr = $totalrural_f_ajcrfp + $totalrural_f_ajcrft;

$PerTotalajcu = $totalurban_f_ajcufp + $totalrural_f_ajcrfp;
$TempoTotalajc = $totalurban_f_ajcuft + $totalrural_f_ajcrft;
$grandTotalajc = $PerTotalajcu + $TempoTotalajc;

// የስራ እድል የተፈጠረላቸው ወጣቶች ኪዎች (Keys) እዚህ ተጨምረዋል

$Rularjc15_29p = $report['rural_m_ajc15_29p'] + $report['rural_f_ajc15_29p'];//ገጠር ቋሚ
$Urbanjc15_29p = $report['urban_m_ajc15_29p'] + $report['urban_f_ajc15_29p'];//ከተማ ቋሚ
$Rularjc15_29t = $report['rural_m_ajc15_29t'] + $report['rural_f_ajc15_29t'];//ገጠር ጊዚያዊ
$Urbanjc15_29t  = $report['urban_m_ajc15_29t'] + $report['urban_f_ajc15_29t'];//ከተማ ጊዚያዊ

$Rularjc15_29mpt = $report['rural_m_ajc15_29p']+$report['rural_m_ajc15_29t'];//ገጠር ወንድ ቋሚና ጊዚያዊ
$Rularjc15_29fpt = $report['rural_f_ajc15_29p']+$report['rural_f_ajc15_29t'];//ገጠር ሴት ቋሚና ጊዚያዊ
$Rularjc15_29pt=$Rularjc15_29mpt+$Rularjc15_29fpt;//ገጠር ቋሚና ጊዚያዊ ድምር

$Urbanjc15_29mpt=$report['urban_m_ajc15_29p']+$report['urban_m_ajc15_29t'];//ከተማ ወንድ ቋሚና ጊዚያዊ
$Urbanjc15_29fpt=$report['urban_f_ajc15_29p']+$report['urban_f_ajc15_29t'];//ከተማ ሴት ቋሚና ጊዚያዊ
$Urbanjc15_29pt=$Urbanjc15_29mpt+$Urbanjc15_29fpt;//ከተማ ቋሚና ጊዚያዊ ድምር

$URjc15_29mp = $report['rural_m_ajc15_29p'] + $report['urban_m_ajc15_29p']; //ከተማና ገጠር ወንድ ቋሚ

$URjc15_29fp = $report['rural_f_ajc15_29p'] + $report['urban_f_ajc15_29p']; //ከተማና ገጠር ሴት ቋሚ
$URjc15_29fpt=$Rularjc15_29fpt+$Urbanjc15_29fpt;//ከተማና ገጠር ቋሚና ጊዚያዊ ሴት

$URjc15_29mt = $report['rural_m_ajc15_29t'] + $report['urban_m_ajc15_29t']; //ከተማና ገጠር ወንድ ጊዚያዊ
$URjc15_29ft = $report['rural_f_ajc15_29t'] + $report['urban_f_ajc15_29t']; //ከተማና ገጠር ሴት ጊዚያዊ

$URjc15_29mpt=$Rularjc15_29mpt+$Urbanjc15_29mpt;//ከተማና ገጠር ቋሚና ጊዚያዊ ወንድ
$URjc15_29fpt=$Rularjc15_29fpt+$Urbanjc15_29fpt;//ከተማና ገጠር ቋሚና ጊዚያዊ ሴት

$URjc15_29p=$URjc15_29mp+$URjc15_29fp;//ከተማ ገጠር ቋሚ ድምር
$URjc15_29t=$URjc15_29mt+$URjc15_29ft;//ከተማ ገጠር ጊዚያዊ ድምር
$URjc15_29pt=$URjc15_29p+$URjc15_29t;//ከተማ ገጠር ጠቅላላ ቋሚና ጊዚያዊ ድምር

// የስራ እድል የተፈጠረላቸው አካል ጉዳተኞች ኪዎች (Keys) እዚህ ተጨምረዋል

$Rularjcphydisp = $report['rural_m_ajcphydisp'] + $report['rural_f_ajcphydisp'];//ገጠር ቋሚ
$Urbanjcphydisp = $report['urban_m_ajcphydisp'] + $report['urban_f_ajcphydisp'];//ከተማ ቋሚ
$Rularjcphydist = $report['rural_m_ajcphydist'] + $report['rural_f_ajcphydist'];//ገጠር ጊዚያዊ
$Urbanjcphydist  = $report['urban_m_ajcphydist'] + $report['urban_f_ajcphydist'];//ከተማ ጊዚያዊ

$Rularjcphydismpt = $report['rural_m_ajcphydisp']+$report['rural_m_ajcphydist'];//ገጠር ወንድ ቋሚና ጊዚያዊ
$Rularjcphydisfpt = $report['rural_f_ajcphydisp']+$report['rural_f_ajcphydist'];//ገጠር ሴት ቋሚና ጊዚያዊ
$Rularjcphydispt=$Rularjcphydismpt+$Rularjcphydisfpt;//ገጠር ቋሚና ጊዚያዊ ድምር

$Urbanjcphydismpt=$report['urban_m_ajcphydisp']+$report['urban_m_ajcphydist'];//ከተማ ወንድ ቋሚና ጊዚያዊ
$Urbanjcphydisfpt=$report['urban_f_ajcphydisp']+$report['urban_f_ajcphydist'];//ከተማ ሴት ቋሚና ጊዚያዊ
$Urbanjcphydispt=$Urbanjcphydismpt+$Urbanjcphydisfpt;//ከተማ ቋሚና ጊዚያዊ ድምር

$URjcphydismp = $report['rural_m_ajcphydisp'] + $report['urban_m_ajcphydisp']; //ከተማና ገጠር ወንድ ቋሚ

$URjcphydisfp = $report['rural_f_ajcphydisp'] + $report['urban_f_ajcphydisp']; //ከተማና ገጠር ሴት ቋሚ
$URjcphydisfpt=$Rularjcphydisfpt+$Urbanjcphydisfpt;//ከተማና ገጠር ቋሚና ጊዚያዊ ሴት

$URjcphydismt = $report['rural_m_ajcphydist'] + $report['urban_m_ajcphydist']; //ከተማና ገጠር ወንድ ጊዚያዊ
$URjcphydisft = $report['rural_f_ajcphydist'] + $report['urban_f_ajcphydist']; //ከተማና ገጠር ሴት ጊዚያዊ

$URjcphydismpt=$Rularjcphydismpt+$Urbanjcphydismpt;//ከተማና ገጠር ቋሚና ጊዚያዊ ወንድ
$URjcphydisfpt=$Rularjcphydisfpt+$Urbanjcphydisfpt;//ከተማና ገጠር ቋሚና ጊዚያዊ ሴት

$URjcphydisp=$URjcphydismp+$URjcphydisfp;//ከተማ ገጠር ቋሚ ድምር
$URjcphydist=$URjcphydismt+$URjcphydisft;//ከተማ ገጠር ጊዚያዊ ድምር
$URjcphydispt=$URjcphydisp+$URjcphydist;//ከተማ ገጠር ጠቅላላ ቋሚና ጊዚያዊ ድምር

// የስራ እድል የተፈጠረላቸው ከስደት ተመላሾች ኪዎች (Keys) እዚህ ተጨምረዋል

$Rularjcimmgp = $report['rural_m_ajcimmgp'] + $report['rural_f_ajcimmgp'];//ገጠር ቋሚ
$Urbanjcimmgp = $report['urban_m_ajcimmgp'] + $report['urban_f_ajcimmgp'];//ከተማ ቋሚ
$Rularjcimmgt = $report['rural_m_ajcimmgt'] + $report['rural_f_ajcimmgt'];//ገጠር ጊዚያዊ
$Urbanjcimmgt  = $report['urban_m_ajcimmgt'] + $report['urban_f_ajcimmgt'];//ከተማ ጊዚያዊ

$Rularjcimmgmpt = $report['rural_m_ajcimmgp']+$report['rural_m_ajcimmgt'];//ገጠር ወንድ ቋሚና ጊዚያዊ
$Rularjcimmgfpt = $report['rural_f_ajcimmgp']+$report['rural_f_ajcimmgt'];//ገጠር ሴት ቋሚና ጊዚያዊ
$Rularjcimmgpt=$Rularjcimmgmpt+$Rularjcimmgfpt;//ገጠር ቋሚና ጊዚያዊ ድምር

$Urbanjcimmgmpt=$report['urban_m_ajcimmgp']+$report['urban_m_ajcimmgt'];//ከተማ ወንድ ቋሚና ጊዚያዊ
$Urbanjcimmgfpt=$report['urban_f_ajcimmgp']+$report['urban_f_ajcimmgt'];//ከተማ ሴት ቋሚና ጊዚያዊ
$Urbanjcimmgpt=$Urbanjcimmgmpt+$Urbanjcimmgfpt;//ከተማ ቋሚና ጊዚያዊ ድምር

$URjcimmgmp = $report['rural_m_ajcimmgp'] + $report['urban_m_ajcimmgp']; //ከተማና ገጠር ወንድ ቋሚ

$URjcimmgfp = $report['rural_f_ajcimmgp'] + $report['urban_f_ajcimmgp']; //ከተማና ገጠር ሴት ቋሚ
$URjcimmgfpt=$Rularjcimmgfpt+$Urbanjcimmgfpt;//ከተማና ገጠር ቋሚና ጊዚያዊ ሴት

$URjcimmgmt = $report['rural_m_ajcimmgt'] + $report['urban_m_ajcimmgt']; //ከተማና ገጠር ወንድ ጊዚያዊ
$URjcimmgft = $report['rural_f_ajcimmgt'] + $report['urban_f_ajcimmgt']; //ከተማና ገጠር ሴት ጊዚያዊ

$URjcimmgmpt=$Rularjcimmgmpt+$Urbanjcimmgmpt;//ከተማና ገጠር ቋሚና ጊዚያዊ ወንድ
$URjcimmgfpt=$Rularjcimmgfpt+$Urbanjcimmgfpt;//ከተማና ገጠር ቋሚና ጊዚያዊ ሴት

$URjcimmgp=$URjcimmgmp+$URjcimmgfp;//ከተማ ገጠር ቋሚ ድምር
$URjcimmgt=$URjcimmgmt+$URjcimmgft;//ከተማ ገጠር ጊዚያዊ ድምር
$URjcimmgpt=$URjcimmgp+$URjcimmgt;//ከተማ ገጠር ጠቅላላ ቋሚና ጊዚያዊ ድምር

// የስራ እድል የተፈጠረላቸው ከሀገር ውስጥ ተፈናቃዮች ኪዎች (Keys) እዚህ ተጨምረዋል

$Rularjcteffp = $report['rural_m_ajcteffp'] + $report['rural_f_ajcteffp'];//ገጠር ቋሚ
$Urbanjcteffp = $report['urban_m_ajcteffp'] + $report['urban_f_ajcteffp'];//ከተማ ቋሚ
$Rularjctefft = $report['rural_m_ajctefft'] + $report['rural_f_ajctefft'];//ገጠር ጊዚያዊ
$Urbanjctefft  = $report['urban_m_ajctefft'] + $report['urban_f_ajctefft'];//ከተማ ጊዚያዊ

$Rularjcteffmpt = $report['rural_m_ajcteffp']+$report['rural_m_ajctefft'];//ገጠር ወንድ ቋሚና ጊዚያዊ
$Rularjctefffpt = $report['rural_f_ajcteffp']+$report['rural_f_ajctefft'];//ገጠር ሴት ቋሚና ጊዚያዊ
$Rularjcteffpt=$Rularjcteffmpt+$Rularjctefffpt;//ገጠር ቋሚና ጊዚያዊ ድምር

$Urbanjcteffmpt=$report['urban_m_ajcteffp']+$report['urban_m_ajctefft'];//ከተማ ወንድ ቋሚና ጊዚያዊ
$Urbanjctefffpt=$report['urban_f_ajcteffp']+$report['urban_f_ajctefft'];//ከተማ ሴት ቋሚና ጊዚያዊ
$Urbanjcteffpt=$Urbanjcteffmpt+$Urbanjctefffpt;//ከተማ ቋሚና ጊዚያዊ ድምር

$URjcteffmp = $report['rural_m_ajcteffp'] + $report['urban_m_ajcteffp']; //ከተማና ገጠር ወንድ ቋሚ

$URjctefffp = $report['rural_f_ajcteffp'] + $report['urban_f_ajcteffp']; //ከተማና ገጠር ሴት ቋሚ
$URjctefffpt=$Rularjctefffpt+$Urbanjctefffpt;//ከተማና ገጠር ቋሚና ጊዚያዊ ሴት

$URjcteffmt = $report['rural_m_ajctefft'] + $report['urban_m_ajctefft']; //ከተማና ገጠር ወንድ ጊዚያዊ
$URjcteffft = $report['rural_f_ajctefft'] + $report['urban_f_ajctefft']; //ከተማና ገጠር ሴት ጊዚያዊ

$URjcteffmpt=$Rularjcteffmpt+$Urbanjcteffmpt;//ከተማና ገጠር ቋሚና ጊዚያዊ ወንድ
$URjctefffpt=$Rularjctefffpt+$Urbanjctefffpt;//ከተማና ገጠር ቋሚና ጊዚያዊ ሴት

$URjcteffp=$URjcteffmp+$URjctefffp;//ከተማ ገጠር ቋሚ ድምር
$URjctefft=$URjcteffmt+$URjcteffft;//ከተማ ገጠር ጊዚያዊ ድምር
$URjcteffpt=$URjcteffp+$URjctefft;//ከተማ ገጠር ጠቅላላ ቋሚና ጊዚያዊ ድምር

// የስራ እድል የተፈጠረላቸው ጎዳና ላይ የሚኖሩ  ኪዎች (Keys) እዚህ ተጨምረዋል

$Rularjcnohp = $report['rural_m_ajcnohp'] + $report['rural_f_ajcnohp'];//ገጠር ቋሚ
$Urbanjcnohp = $report['urban_m_ajcnohp'] + $report['urban_f_ajcnohp'];//ከተማ ቋሚ
$Rularjcnoht = $report['rural_m_ajcnoht'] + $report['rural_f_ajcnoht'];//ገጠር ጊዚያዊ
$Urbanjcnoht  = $report['urban_m_ajcnoht'] + $report['urban_f_ajcnoht'];//ከተማ ጊዚያዊ

$Rularjcnohmpt = $report['rural_m_ajcnohp']+$report['rural_m_ajcnoht'];//ገጠር ወንድ ቋሚና ጊዚያዊ
$Rularjcnohfpt = $report['rural_f_ajcnohp']+$report['rural_f_ajcnoht'];//ገጠር ሴት ቋሚና ጊዚያዊ
$Rularjcnohpt=$Rularjcnohmpt+$Rularjcnohfpt;//ገጠር ቋሚና ጊዚያዊ ድምር

$Urbanjcnohmpt=$report['urban_m_ajcnohp']+$report['urban_m_ajcnoht'];//ከተማ ወንድ ቋሚና ጊዚያዊ
$Urbanjcnohfpt=$report['urban_f_ajcnohp']+$report['urban_f_ajcnoht'];//ከተማ ሴት ቋሚና ጊዚያዊ
$Urbanjcnohpt=$Urbanjcnohmpt+$Urbanjcnohfpt;//ከተማ ቋሚና ጊዚያዊ ድምር

$URjcnohmp = $report['rural_m_ajcnohp'] + $report['urban_m_ajcnohp']; //ከተማና ገጠር ወንድ ቋሚ

$URjcnohfp = $report['rural_f_ajcnohp'] + $report['urban_f_ajcnohp']; //ከተማና ገጠር ሴት ቋሚ
$URjcnohfpt=$Rularjcnohfpt+$Urbanjcnohfpt;//ከተማና ገጠር ቋሚና ጊዚያዊ ሴት

$URjcnohmt = $report['rural_m_ajcnoht'] + $report['urban_m_ajcnoht']; //ከተማና ገጠር ወንድ ጊዚያዊ
$URjcnohft = $report['rural_f_ajcnoht'] + $report['urban_f_ajcnoht']; //ከተማና ገጠር ሴት ጊዚያዊ

$URjcnohmpt=$Rularjcnohmpt+$Urbanjcnohmpt;//ከተማና ገጠር ቋሚና ጊዚያዊ ወንድ
$URjcnohfpt=$Rularjcnohfpt+$Urbanjcnohfpt;//ከተማና ገጠር ቋሚና ጊዚያዊ ሴት

$URjcnohp=$URjcnohmp+$URjcnohfp;//ከተማ ገጠር ቋሚ ድምር
$URjcnoht=$URjcnohmt+$URjcnohft;//ከተማ ገጠር ጊዚያዊ ድምር
$URjcnohpt=$URjcnohp+$URjcnoht;//ከተማ ገጠር ጠቅላላ ቋሚና ጊዚያዊ ድምር

// የስራ እድል የተፈጠረላቸው የዩኒቨርሲቲ ተመራቂዎች  ኪዎች (Keys) እዚህ ተጨምረዋል

$Rularjcunip = $report['rural_m_ajcunip'] + $report['rural_f_ajcunip'];//ገጠር ቋሚ
$Urbanjcunip = $report['urban_m_ajcunip'] + $report['urban_f_ajcunip'];//ከተማ ቋሚ
$Rularjcunit = $report['rural_m_ajcunit'] + $report['rural_f_ajcunit'];//ገጠር ጊዚያዊ
$Urbanjcunit  = $report['urban_m_ajcunit'] + $report['urban_f_ajcunit'];//ከተማ ጊዚያዊ

$Rularjcunimpt = $report['rural_m_ajcunip']+$report['rural_m_ajcunit'];//ገጠር ወንድ ቋሚና ጊዚያዊ
$Rularjcunifpt = $report['rural_f_ajcunip']+$report['rural_f_ajcunit'];//ገጠር ሴት ቋሚና ጊዚያዊ
$Rularjcunipt=$Rularjcunimpt+$Rularjcunifpt;//ገጠር ቋሚና ጊዚያዊ ድምር

$Urbanjcunimpt=$report['urban_m_ajcunip']+$report['urban_m_ajcunit'];//ከተማ ወንድ ቋሚና ጊዚያዊ
$Urbanjcunifpt=$report['urban_f_ajcunip']+$report['urban_f_ajcunit'];//ከተማ ሴት ቋሚና ጊዚያዊ
$Urbanjcunipt=$Urbanjcunimpt+$Urbanjcunifpt;//ከተማ ቋሚና ጊዚያዊ ድምር

$URjcunimp = $report['rural_m_ajcunip'] + $report['urban_m_ajcunip']; //ከተማና ገጠር ወንድ ቋሚ

$URjcunifp = $report['rural_f_ajcunip'] + $report['urban_f_ajcunip']; //ከተማና ገጠር ሴት ቋሚ
$URjcunifpt=$Rularjcunifpt+$Urbanjcunifpt;//ከተማና ገጠር ቋሚና ጊዚያዊ ሴት

$URjcunimt = $report['rural_m_ajcunit'] + $report['urban_m_ajcunit']; //ከተማና ገጠር ወንድ ጊዚያዊ
$URjcunift = $report['rural_f_ajcunit'] + $report['urban_f_ajcunit']; //ከተማና ገጠር ሴት ጊዚያዊ

$URjcunimpt=$Rularjcunimpt+$Urbanjcunimpt;//ከተማና ገጠር ቋሚና ጊዚያዊ ወንድ
$URjcunifpt=$Rularjcunifpt+$Urbanjcunifpt;//ከተማና ገጠር ቋሚና ጊዚያዊ ሴት

$URjcunip=$URjcunimp+$URjcunifp;//ከተማ ገጠር ቋሚ ድምር
$URjcunit=$URjcunimt+$URjcunift;//ከተማ ገጠር ጊዚያዊ ድምር
$URjcunipt=$URjcunip+$URjcunit;//ከተማ ገጠር ጠቅላላ ቋሚና ጊዚያዊ ድምር

// የስራ እድል የተፈጠረላቸው የቴ/ሙ ተመራቂዎች  ኪዎች (Keys) እዚህ ተጨምረዋል

$Rularjctvtp = $report['rural_m_ajctvtp'] + $report['rural_f_ajctvtp'];//ገጠር ቋሚ
$Urbanjctvtp = $report['urban_m_ajctvtp'] + $report['urban_f_ajctvtp'];//ከተማ ቋሚ
$Rularjctvtt = $report['rural_m_ajctvtt'] + $report['rural_f_ajctvtt'];//ገጠር ጊዚያዊ
$Urbanjctvtt  = $report['urban_m_ajctvtt'] + $report['urban_f_ajctvtt'];//ከተማ ጊዚያዊ

$Rularjctvtmpt = $report['rural_m_ajctvtp']+$report['rural_m_ajctvtt'];//ገጠር ወንድ ቋሚና ጊዚያዊ
$Rularjctvtfpt = $report['rural_f_ajctvtp']+$report['rural_f_ajctvtt'];//ገጠር ሴት ቋሚና ጊዚያዊ
$Rularjctvtpt=$Rularjctvtmpt+$Rularjctvtfpt;//ገጠር ቋሚና ጊዚያዊ ድምር

$Urbanjctvtmpt=$report['urban_m_ajctvtp']+$report['urban_m_ajctvtt'];//ከተማ ወንድ ቋሚና ጊዚያዊ
$Urbanjctvtfpt=$report['urban_f_ajctvtp']+$report['urban_f_ajctvtt'];//ከተማ ሴት ቋሚና ጊዚያዊ
$Urbanjctvtpt=$Urbanjctvtmpt+$Urbanjctvtfpt;//ከተማ ቋሚና ጊዚያዊ ድምር

$URjctvtmp = $report['rural_m_ajctvtp'] + $report['urban_m_ajctvtp']; //ከተማና ገጠር ወንድ ቋሚ

$URjctvtfp = $report['rural_f_ajctvtp'] + $report['urban_f_ajctvtp']; //ከተማና ገጠር ሴት ቋሚ
$URjctvtfpt=$Rularjctvtfpt+$Urbanjctvtfpt;//ከተማና ገጠር ቋሚና ጊዚያዊ ሴት

$URjctvtmt = $report['rural_m_ajctvtt'] + $report['urban_m_ajctvtt']; //ከተማና ገጠር ወንድ ጊዚያዊ
$URjctvtft = $report['rural_f_ajctvtt'] + $report['urban_f_ajctvtt']; //ከተማና ገጠር ሴት ጊዚያዊ

$URjctvtmpt=$Rularjctvtmpt+$Urbanjctvtmpt;//ከተማና ገጠር ቋሚና ጊዚያዊ ወንድ
$URjctvtfpt=$Rularjctvtfpt+$Urbanjctvtfpt;//ከተማና ገጠር ቋሚና ጊዚያዊ ሴት

$URjctvtp=$URjctvtmp+$URjctvtfp;//ከተማ ገጠር ቋሚ ድምር
$URjctvtt=$URjctvtmt+$URjctvtft;//ከተማ ገጠር ጊዚያዊ ድምር
$URjctvtpt=$URjctvtp+$URjctvtt;//ከተማ ገጠር ጠቅላላ ቋሚና ጊዚያዊ ድምር


$startdate = !empty($startdate) ? $startdate : date('Y-m-d');
$startdateParts = explode('-', $startdate);
$ethstartDate = EthiopianDateHelper::toEthCalendar($startdateParts[2], $startdateParts[1], $startdateParts[0]);

$enddate = !empty($enddate) ? $enddate : date('Y-m-d');
$enddateParts = explode('-', $enddate);
$ethendDate = EthiopianDateHelper::toEthCalendar($enddateParts[2], $enddateParts[1], $enddateParts[0]);

// 🟢 EXPORT LOGIC: Intercepts and downloads as .xlsx if requested
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
    header("Content-Disposition: attachment; filename=Inclusive_Report_W10.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    // Ensure Amharic UTF-8 characters display correctly in Excel
    echo "\xEF\xBB\xBF"; 
}
?>

<style>
    body { padding: 30px; background-color: #fff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #000; }
    .table { width: 100%; border-collapse: collapse; }
    .table th { background-color: #f8f9fa; text-align: center; vertical-align: middle !important; font-size: 11px; font-weight: bold; border: 1px solid #000 !important; padding: 4px; }
    .table td { vertical-align: middle !important; font-size: 11px; border: 1px solid #000 !important; padding: 4px; }
    .report-header { text-align: center; margin-bottom: 20px; }
    .text-left { text-align: left !important; padding-left: 8px !important; }
    
    @media print {
        body { padding: 0; }
        .no-print { display: none; }
    }
</style>

<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="UTF-8">
    <title>የስራ እድል ፈጠራ አካታችነት ሪፖርት በከተማና ገጠር (ሠ10)</title>
    <style>
        .report-container {
            width: 100%;
            margin: 0 auto;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }
        .report-header {
            text-align: center;
            margin-bottom: 10px;
        }
        .report-header h2 {
            margin: 2px 0;
            font-size: 16px;
        }
        .report-header h3 {
            margin: 2px 0;
            font-size: 13px;
            color: #555;
        }
        .table-responsive {
            overflow-x: auto;
            margin-bottom: 15px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            white-space: nowrap;
        }
        .report-table th, .report-table td {
            border: 1px solid #444;
            padding: 4px 5px;
            text-align: center;
        }
        .report-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .bg-group {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .bg-total {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            font-family: sans-serif;
            font-size: 13px;
            border: none;
            cursor: pointer;
        }
        
        @media print {
            @page {
                size: A4 landscape;
                margin: 5mm 8mm;
            }
            html, body {
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
                background-color: #fff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print { 
                display: none !important; 
            }
            .table-responsive {
                overflow: visible !important;
                margin-bottom: 0;
            }
            .report-table { 
                width: 100% !important;
                font-size: 9px;
                table-layout: fixed;
            }
            .report-table th, .report-table td { 
                padding: 2px 3px !important;
                border: 1px solid #000 !important;
            }
        }
    </style>
</head>
<body>

<div class="report-container">
    
    <!-- ኤክስፖርት ማድረጊያ በተን -->
    <?php if (!isset($_GET['export'])): ?>
    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <a href="?export=excel&branch_id=<?= urlencode($myBranchId ?? ''); ?>&start_date=<?= urlencode($startdate ?? ''); ?>&end_date=<?= urlencode($enddate ?? ''); ?>" 
           class="btn btn-success">
            Export to Excel (.xlsx)
        </a>
    </div>
    <?php endif; ?>

<!-- የሪፖርቱ ራስጌ (Header) -->
<center class="mb-4">
    <h4 class="font-weight-bold">የስራ እድል ፈጠራ አካታችነት ሪፖርት በከተማና ገጠር (ሠ10)</h4>
    <h5 class="text-primary mt-2">የመዋቅር ደረጃ፦ <strong><?= htmlspecialchars($selectedBranchName); ?></strong></h5>
    <h6>
        የሪፖርት ቀን፦
        <?php if (isset($ethstartDate) && is_array($ethstartDate) && isset($ethstartDate['month'], $ethstartDate['day'], $ethstartDate['year'])): ?>
            <?= EthiopianDateHelper::getMonthName($ethstartDate['month']) ?>
            <?= $ethstartDate['day'] ?>
            <?= $ethstartDate['year'] ?>
        <?php else: ?>
            <?= htmlspecialchars($startdate ?? '') ?>
        <?php endif; ?>

        <?php if (isset($startdate, $enddate) && $startdate != $enddate): ?>
            -
            <?php if (isset($ethendDate) && is_array($ethendDate) && isset($ethendDate['month'], $ethendDate['day'], $ethendDate['year'])): ?>
                <?= EthiopianDateHelper::getMonthName($ethendDate['month']) ?>
                <?= $ethendDate['day'] ?>
                <?= $ethendDate['year'] ?>
            <?php else: ?>
                <?= htmlspecialchars($enddate ?? '') ?>
            <?php endif; ?>
        <?php endif; ?>
    </h6>
</center>
    
    <div class="table-responsive">
        <table class="report-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 12%;">አመላካች</th>
                    <th colspan="2" style="width: 18%;">መለኪያ</th>
                    <th rowspan="2" style="width: 16%;">የስራ ፈላጊዎች ምዝገባ</th>
                    <th rowspan="2" style="width: 16%;">ግንዛቤ ማስጨበጫ</th>
                    <th colspan="3" style="width: 38%;">ስራ እድል ፈጠራ</th>
                </tr>
                <tr>
                    <th>አካባቢ</th>
                    <th>ጾታ</th>
                    <th>ቋሚ</th>
                    <th>ጊዜያዊ</th>
                    <th>ድምር</th>
                </tr>
            </thead>
            <tbody>

                <!-- ================= 1. የሴቶች አመላካች ================= -->
                <tr>
                    <td rowspan="3">የሴቶች</td>
                    <td>ገጠር</td>
                    <td>ሴት</td>
                    <td><?= $report['rural_f_advice']; ?></td>
                    <td><?= $report['rural_f_ajs']; ?></td>
                    <td><?= $report['rural_f_ajcrfp']; ?></td>
                    <td><?= $report['rural_f_ajcrft']; ?></td>
                    <td><?= $Totalajcr; ?></td>
                </tr>
                <tr>
                    <td>ከተማ</td>
                    <td>ሴት</td>
                    <td><?= $report['urban_f_advice']; ?></td>
                    <td><?= $report['urban_f_ajs']; ?></td>
                    <td><?= $report['urban_f_ajcufp']; ?></td>
                    <td><?= $report['urban_f_ajcuft']; ?></td>
                    <td><?= $Totalajcu; ?></td>
                </tr>
                <tr>
                    <td>ከተማ እና ገጠር ድምር</td>
                    <td>ሴት</td>
                    <td><?= $totalFemaleadvice; ?></td>
                    <td><?= $totalFemaleajs; ?></td>
                    <td><?= $PerTotalajcu; ?></td>
                    <td><?= $TempoTotalajc; ?></td>
                    <td><?= $grandTotalajc; ?></td>
                </tr>
                <!-- ================= 2. የወጣቶች አመላካች ================= -->
                                        <tr>
                        <td rowspan="9">ወጣቶች</td>
                        <td rowspan="3">ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $report['rural_m_age15_29']; ?></td>
                        <td><?= $report['rural_m_ajs15_29']; ?></td>
                        <td><?= $report['rural_m_ajc15_29p']; ?></td>
                        <td><?= $report['rural_m_ajc15_29t']; ?></td>
                        <td><?= $Rularjc15_29mpt; ?></td>
                        </tr>

                        <!-- Row 2  -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['rural_f_age15_29']; ?></td>
                        <td><?= $report['rural_f_ajs15_29']; ?></td>
                        <td><?= $report['rural_f_ajc15_29p']; ?></td>
                        <td><?= $report['rural_f_ajc15_29t']; ?></td>
                        <td><?= $Rularjc15_29fpt; ?></td>
                        </tr>

                        <!-- Row 3 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalRuralage15_29; ?></td>
                        <td><?= $totalRuralajs15_29; ?></td>
                        <td><?= $Rularjc15_29p; ?></td>
                        <td><?= $Rularjc15_29t; ?></td>
                        <td><?= $Rularjc15_29pt; ?></td>
                        </tr>

                        <!-- Row 4 -->
                        <tr>
                        <td rowspan="3">ከተማ</td>
                        <td>ወንድ</td>
                        <td><?= $report['urban_m_age15_29']; ?></td>
                        <td><?= $report['urban_m_ajs15_29']; ?></td>
                        <td><?= $report['urban_m_ajc15_29p']; ?></td>
                        <td><?= $report['urban_m_ajc15_29t']; ?></td>
                        <td><?= $Urbanjc15_29mpt; ?></td>
                        </tr>

                        <!-- Row 5 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['urban_f_age15_29']; ?></td>
                        <td><?= $report['urban_f_ajs15_29']; ?></td>
                        <td><?= $report['urban_f_ajc15_29p']; ?></td>
                        <td><?= $report['urban_f_ajc15_29t']; ?></td>
                        <td><?= $Urbanjc15_29fpt; ?></td>
                        </tr>

                        <!-- Row 6 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalUrbanage15_29; ?></td>
                        <td><?= $totalUrbanajs15_29; ?></td>
                        <td><?= $Urbanjc15_29p; ?></td>
                        <td><?= $Urbanjc15_29t; ?></td>
                        <td><?= $Urbanjc15_29pt; ?></td>
                        </tr>

                        <!-- Row 7 -->
                        <tr>
                        <td rowspan="3">ከተማ እና ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $totalMaleage15_29; ?></td>
                        <td><?= $totalMaleajs15_29; ?></td>
                        <td><?= $URjc15_29mp; ?></td>
                        <td><?= $URjc15_29mt; ?></td>
                        <td><?= $URjc15_29mpt; ?></td>
                        </tr>

                        <!-- Row 8 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $totalFemaleage15_29; ?></td>
                        <td><?= $totalFemaleajs15_29; ?></td>
                        <td><?= $URjc15_29fp; ?></td>
                        <td><?= $URjc15_29ft; ?></td>
                        <td><?= $URjc15_29fpt; ?></td>
                        </tr>


                        <!-- Row 9 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $grandTotalage15_29; ?></td>
                        <td><?= $grandTotalajs15_29; ?></td>
                        <td><?=$URjc15_29p; ?></td>
                        <td><?= $URjc15_29t; ?></td>
                        <td><?= $URjc15_29pt; ?></td>
                        </tr>


                <!-- ================= 3. አካል ጉዳተኞች አመላካች ================= -->
                                        <tr>
                        <td rowspan="9">አካል ጉዳተኞች</td>
                        <td rowspan="3">ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $report['rural_m_phy']; ?></td>
                        <td><?= $report['rural_m_ajsdis']; ?></td>
                        <td><?= $report['rural_m_ajcphydisp']; ?></td>
                        <td><?= $report['rural_m_ajcphydist']; ?></td>
                        <td><?= $Rularjcphydismpt; ?></td>
                        </tr>

                        <!-- Row 2 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['rural_f_phy']; ?></td>
                        <td><?= $report['rural_f_ajsdis']; ?></td>
                        <td><?= $report['rural_f_ajcphydisp']; ?></td>
                        <td><?= $report['rural_f_ajcphydist']; ?></td>
                        <td><?= $Rularjcphydisfpt; ?></td>
                        </tr>

                        <!-- Row 3 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalRuralphy; ?></td>
                        <td><?= $totalRuralajsdis; ?></td>
                        <td><?= $Rularjcphydisp; ?></td>
                        <td><?= $Rularjcphydist; ?></td>
                        <td><?= $Rularjcphydispt; ?></td>
                        </tr>

                        <!-- Row 4 -->
                        <tr>
                        <td rowspan="3">ከተማ</td>
                        <td>ወንድ</td>
                        <td><?= $report['urban_m_phy']; ?></td>
                        <td><?= $report['urban_m_ajsdis']; ?></td>
                        <td><?= $report['urban_m_ajcphydisp']; ?></td>
                        <td><?= $report['urban_m_ajcphydist']; ?></td>
                        <td><?= $Urbanjcphydismpt; ?></td>
                        </tr>

                        <!-- Row 5 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['urban_f_phy']; ?></td>
                        <td><?= $report['urban_f_ajsdis']; ?></td>
                        <td><?= $report['urban_f_ajcphydisp']; ?></td>
                        <td><?= $report['urban_f_ajcphydist']; ?></td>
                        <td><?= $Urbanjcphydisfpt; ?></td>
                        </tr>

                        <!-- Row 6 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalUrbanphy; ?></td>
                        <td><?= $totalUrbanajsdis; ?></td>
                        <td><?= $Urbanjcphydisp; ?></td>
                        <td><?= $Urbanjcphydist; ?></td>
                        <td><?= $Urbanjcphydispt; ?></td>
                        </tr>

                        <!-- Row 7 -->
                        <tr>
                        <td rowspan="3">ከተማ እና ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $totalMalephy; ?></td>
                        <td><?= $totalMaleajsdis; ?></td>
                        <td><?= $URjcphydismp; ?></td>
                        <td><?= $URjcphydismt; ?></td>
                        <td><?= $URjcphydismpt; ?></td>
                        </tr>

                        <!-- Row 8 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $totalFemalephy; ?></td>
                        <td><?= $totalFemaleajsdis; ?></td>
                        <td><?= $URjcphydisfp; ?></td>
                        <td><?= $URjcphydisft; ?></td>
                        <td><?= $URjcphydisfpt; ?></td>
                        </tr>

                        <!-- Row 9 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $grandTotalphy; ?></td>
                        <td><?= $grandTotalajsdis; ?></td>
                        <td><?=$URjcphydisp; ?></td>
                        <td><?= $URjcphydist; ?></td>
                        <td><?= $URjcphydispt; ?></td>
                        </tr>
        <!-- ================= 4. ከስደት ተመላሽ ዜጎች አመላካች ================= -->
                                        <tr>
                        <td rowspan="9">ከስደት ተመላሽ ዜጎች</td>
                        <td rowspan="3">ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $report['rural_m_immg']; ?></td>
                        <td><?= $report['rural_m_ajsimmg']; ?></td>
                        <td><?= $report['rural_m_ajcimmgp']; ?></td>
                        <td><?= $report['rural_m_ajcimmgt']; ?></td>
                        <td><?= $Rularjcimmgpt; ?></td>
                        </tr>

                        <!-- Row 2 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['rural_f_immg']; ?></td>
                        <td><?= $report['rural_f_ajsimmg']; ?></td>
                        <td><?= $report['rural_f_ajcimmgp']; ?></td>
                        <td><?= $report['rural_f_ajcimmgt']; ?></td>
                        <td><?= $Rularjcimmgfpt; ?></td>
                        </tr>

                        <!-- Row 3 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalRuralimmg; ?></td>
                        <td><?= $totalRuralajsimmg; ?></td>
                        <td><?= $Rularjcimmgp; ?></td>
                        <td><?= $Rularjcimmgt; ?></td>
                        <td><?= $Rularjcimmgpt; ?></td>
                        </tr>

                        <!-- Row 4 -->
                        <tr>
                        <td rowspan="3">ከተማ</td>
                        <td>ወንድ</td>
                        <td><?= $report['urban_m_immg']; ?></td>
                        <td><?= $report['urban_m_ajsimmg']; ?></td>
                        <td><?= $report['urban_m_ajcimmgp']; ?></td>
                        <td><?= $report['urban_m_ajcimmgt']; ?></td>
                        <td><?= $Urbanjcimmgmpt; ?></td>
                        </tr>

                        <!-- Row 5 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['urban_f_immg']; ?></td>
                        <td><?= $report['urban_f_ajsimmg']; ?></td>
                        <td><?= $report['urban_f_ajcimmgp']; ?></td>
                        <td><?= $report['urban_f_ajcimmgt']; ?></td>
                        <td><?= $Urbanjcimmgfpt; ?></td>
                        </tr>

                        <!-- Row 6 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalUrbanimmg; ?></td>
                        <td><?= $totalUrbanajsimmg; ?></td>
                        <td><?= $Urbanjcimmgp; ?></td>
                        <td><?= $Urbanjcimmgt; ?></td>
                        <td><?= $Urbanjcimmgpt; ?></td>
                        </tr>

                        <!-- Row 7 -->
                        <tr>
                        <td rowspan="3">ከተማ እና ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $totalMaleimmg; ?></td>
                        <td><?= $totalMaleajsimmg; ?></td>
                        <td><?= $URjcimmgmp; ?></td>
                        <td><?= $URjcimmgmt; ?></td>
                        <td><?= $URjcimmgmpt; ?></td>
                        </tr>

                        <!-- Row 8 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $totalFemaleimmg; ?></td>
                        <td><?= $totalFemaleajsimmg; ?></td>
                        <td><?= $URjcimmgfp; ?></td>
                        <td><?= $URjcimmgft; ?></td>
                        <td><?= $URjcimmgfpt; ?></td>
                        </tr>

                        <!-- Row 9 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $grandTotalimmg; ?></td>
                        <td><?= $grandTotalajsimmg; ?></td>
                        <td><?=$URjcimmgp; ?></td>
                        <td><?= $URjcimmgt; ?></td>
                        <td><?= $URjcimmgpt; ?></td>
                        </tr>
        <!-- ================= 5. የሀገር ውስጥ ተፈናቃይ አመላካች ================= -->
                                        <tr>
                        <td rowspan="9">የሀገር ውስጥ ተፈናቃይ</td>
                        <td rowspan="3">ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $report['rural_m_teff']; ?></td>
                        <td><?= $report['rural_m_ajsteff']; ?></td>
                        <td><?= $report['rural_m_ajcteffp']; ?></td>
                        <td><?= $report['rural_m_ajctefft']; ?></td>
                        <td><?= $Rularjcteffpt; ?></td>
                        </tr>

                        <!-- Row 2 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['rural_f_teff']; ?></td>
                        <td><?= $report['rural_f_ajsteff']; ?></td>
                        <td><?= $report['rural_f_ajcteffp']; ?></td>
                        <td><?= $report['rural_f_ajctefft']; ?></td>
                        <td><?= $Rularjctefffpt; ?></td>
                        </tr>

                        <!-- Row 3 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalRuralteff; ?></td>
                        <td><?= $totalRuralajsteff; ?></td>
                        <td><?= $Rularjcteffp; ?></td>
                        <td><?= $Rularjctefft; ?></td>
                        <td><?= $Rularjcteffpt; ?></td>
                        </tr>

                        <!-- Row 4 -->
                        <tr>
                        <td rowspan="3">ከተማ</td>
                        <td>ወንድ</td>
                        <td><?= $report['urban_m_teff']; ?></td>
                        <td><?= $report['urban_m_ajsteff']; ?></td>
                        <td><?= $report['urban_m_ajcteffp']; ?></td>
                        <td><?= $report['urban_m_ajctefft']; ?></td>
                        <td><?= $Urbanjcteffmpt; ?></td>
                        </tr>

                        <!-- Row 5 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['urban_f_teff']; ?></td>
                        <td><?= $report['urban_f_ajsteff']; ?></td>
                        <td><?= $report['urban_f_ajcteffp']; ?></td>
                        <td><?= $report['urban_f_ajctefft']; ?></td>
                        <td><?= $Urbanjctefffpt; ?></td>
                        </tr>

                        <!-- Row 6 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalUrbanteff; ?></td>
                        <td><?= $totalUrbanajsteff; ?></td>
                        <td><?= $Urbanjcteffp; ?></td>
                        <td><?= $Urbanjctefft; ?></td>
                        <td><?= $Urbanjcteffpt; ?></td>
                        </tr>

                        <!-- Row 7 -->
                        <tr>
                        <td rowspan="3">ከተማ እና ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $totalMaleteff; ?></td>
                        <td><?= $totalMaleajsteff; ?></td>
                        <td><?= $URjcteffmp; ?></td>
                        <td><?= $URjcteffmt; ?></td>
                        <td><?= $URjcteffmpt; ?></td>
                        </tr>

                        <!-- Row 8 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $totalFemaleteff; ?></td>
                        <td><?= $totalFemaleajsteff; ?></td>
                        <td><?= $URjctefffp; ?></td>
                        <td><?= $URjcteffft; ?></td>
                        <td><?= $URjctefffpt; ?></td>
                        </tr>

                        <!-- Row 9 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $grandTotalteff; ?></td>
                        <td><?= $grandTotalajsteff; ?></td>
                        <td><?=$URjcteffp; ?></td>
                        <td><?= $URjctefft; ?></td>
                        <td><?= $URjcteffpt; ?></td>
                        </tr>
        <!-- ================= 6. መኖሪያቸው ጎዳና የሆኑ ዜጎች አመላካች ================= -->
                        <tr>
                        <td rowspan="9">መኖሪያቸው ጎዳና የሆኑ ዜጎች</td>
                        <td rowspan="3">ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $report['rural_m_noh']; ?></td>
                        <td><?= $report['rural_m_ajsnoh']; ?></td>
                        <td><?= $report['rural_m_ajcnohp']; ?></td>
                        <td><?= $report['rural_m_ajcnoht']; ?></td>
                        <td><?= $Rularjcnohpt; ?></td>
                        </tr>

                        <!-- Row 2 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['rural_f_noh']; ?></td>
                        <td><?= $report['rural_f_ajsnoh']; ?></td>
                        <td><?= $report['rural_f_ajcnohp']; ?></td>
                        <td><?= $report['rural_f_ajcnoht']; ?></td>
                        <td><?= $Rularjcnohfpt; ?></td>
                        </tr>

                        <!-- Row 3 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalRuralnoh; ?></td>
                        <td><?= $totalRuralajsnoh; ?></td>
                        <td><?= $Rularjcteffp; ?></td>
                        <td><?= $Rularjctefft; ?></td>
                        <td><?= $Rularjcteffpt; ?></td>
                        </tr>

                        <!-- Row 4 -->
                        <tr>
                        <td rowspan="3">ከተማ</td>
                        <td>ወንድ</td>
                        <td><?= $report['urban_m_noh']; ?></td>
                        <td><?= $report['urban_m_ajsnoh']; ?></td>
                        <td><?= $report['urban_m_ajcnohp']; ?></td>
                        <td><?= $report['urban_m_ajcnoht']; ?></td>
                        <td><?= $Urbanjcnohmpt; ?></td>
                        </tr>

                        <!-- Row 5 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['urban_f_noh']; ?></td>
                        <td><?= $report['urban_f_ajsnoh']; ?></td>
                        <td><?= $report['urban_f_ajcnohp']; ?></td>
                        <td><?= $report['urban_f_ajcnoht']; ?></td>
                        <td><?= $Urbanjcnohfpt; ?></td>
                        </tr>

                        <!-- Row 6 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalUrbannoh; ?></td>
                        <td><?= $totalUrbanajsnoh; ?></td>
                        <td><?= $Urbanjcnohp; ?></td>
                        <td><?= $Urbanjcnoht; ?></td>
                        <td><?= $Urbanjcnohpt; ?></td>
                        </tr>

                        <!-- Row 7 -->
                        <tr>
                        <td rowspan="3">ከተማ እና ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $totalMalenoh; ?></td>
                        <td><?= $totalMaleajsnoh; ?></td>
                        <td><?= $URjcnohmp; ?></td>
                        <td><?= $URjcnohmt; ?></td>
                        <td><?= $URjcnohmpt; ?></td>
                        </tr>

                        <!-- Row 8 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $totalFemalenoh; ?></td>
                        <td><?= $totalFemaleajsnoh; ?></td>
                        <td><?= $URjcnohfp; ?></td>
                        <td><?= $URjcnohft; ?></td>
                        <td><?= $URjcnohfpt; ?></td>
                        </tr>

                        <!-- Row 9 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $grandTotalnoh; ?></td>
                        <td><?= $grandTotalajsnoh; ?></td>
                        <td><?=$URjcnohp; ?></td>
                        <td><?= $URjcnoht; ?></td>
                        <td><?= $URjcnohpt; ?></td>
                        </tr>
        <!-- ================= 7. ከዩኒቨርሲቲ ተመራቂዎች አመላካች ================= -->
                        <tr>
                        <td rowspan="9">ከዩኒቨርሲቲ ተመራቂዎች</td>
                        <td rowspan="3">ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $report['rural_m_uni']; ?></td>
                        <td><?= $report['rural_m_ajsuni']; ?></td>
                        <td><?= $report['rural_m_ajcunip']; ?></td>
                        <td><?= $report['rural_m_ajcunit']; ?></td>
                        <td><?= $Rularjcunipt; ?></td>
                        </tr>

                        <!-- Row 2 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['rural_f_uni']; ?></td>
                        <td><?= $report['rural_f_ajsuni']; ?></td>
                        <td><?= $report['rural_f_ajcunip']; ?></td>
                        <td><?= $report['rural_f_ajcunit']; ?></td>
                        <td><?= $Rularjcunifpt; ?></td>
                        </tr>

                        <!-- Row 3 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalRuraluni; ?></td>
                        <td><?= $totalRuralajsuni; ?></td>
                        <td><?= $Rularjcunip; ?></td>
                        <td><?= $Rularjcunit; ?></td>
                        <td><?= $Rularjcunipt; ?></td>
                        </tr>

                        <!-- Row 4 -->
                        <tr>
                        <td rowspan="3">ከተማ</td>
                        <td>ወንድ</td>
                        <td><?= $report['urban_m_uni']; ?></td>
                        <td><?= $report['urban_m_ajsuni']; ?></td>
                        <td><?= $report['urban_m_ajcunip']; ?></td>
                        <td><?= $report['urban_m_ajcunit']; ?></td>
                        <td><?= $Urbanjcunimpt; ?></td>
                        </tr>

                        <!-- Row 5 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['urban_f_uni']; ?></td>
                        <td><?= $report['urban_f_ajsuni']; ?></td>
                        <td><?= $report['urban_f_ajcunip']; ?></td>
                        <td><?= $report['urban_f_ajcunit']; ?></td>
                        <td><?= $Urbanjcunifpt; ?></td>
                        </tr>

                        <!-- Row 6 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalUrbanuni; ?></td>
                        <td><?= $totalUrbanajsuni; ?></td>
                        <td><?= $Urbanjcunip; ?></td>
                        <td><?= $Urbanjcunit; ?></td>
                        <td><?= $Urbanjcunipt; ?></td>
                        </tr>

                        <!-- Row 7 -->
                        <tr>
                        <td rowspan="3">ከተማ እና ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $totalMaleuni; ?></td>
                        <td><?= $totalMaleajsuni; ?></td>
                        <td><?= $URjcunimp; ?></td>
                        <td><?= $URjcunimt; ?></td>
                        <td><?= $URjcunimpt; ?></td>
                        </tr>

                        <!-- Row 8 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $totalFemaleuni; ?></td>
                        <td><?= $totalFemaleajsuni; ?></td>
                        <td><?= $URjcunifp; ?></td>
                        <td><?= $URjcunift; ?></td>
                        <td><?= $URjcunifpt; ?></td>
                        </tr>

                        <!-- Row 9 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $grandTotaluni; ?></td>
                        <td><?= $grandTotalajsuni; ?></td>
                        <td><?=$URjcunip; ?></td>
                        <td><?= $URjcunit; ?></td>
                        <td><?= $URjcunipt; ?></td>
                        </tr>
        <!-- ================= 8. ከቴክኒክ እና ሙያ ተመራቂዎች አመላካች ================= -->
                        <tr>
                        <td rowspan="9">ከቴክኒክ እና ሙያ ተመራቂዎች</td>
                        <td rowspan="3">ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $report['rural_m_tvt']; ?></td>
                        <td><?= $report['rural_m_ajstvt']; ?></td>
                        <td><?= $report['rural_m_ajctvtp']; ?></td>
                        <td><?= $report['rural_m_ajctvtt']; ?></td>
                        <td><?= $Rularjctvtpt; ?></td>
                        </tr>

                        <!-- Row 2 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['rural_f_tvt']; ?></td>
                        <td><?= $report['rural_f_ajstvt']; ?></td>
                        <td><?= $report['rural_f_ajctvtp']; ?></td>
                        <td><?= $report['rural_f_ajctvtt']; ?></td>
                        <td><?= $Rularjctvtfpt; ?></td>
                        </tr>

                        <!-- Row 3 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalRuraltvt; ?></td>
                        <td><?= $totalRuralajstvt; ?></td>
                        <td><?= $Rularjctvtp; ?></td>
                        <td><?= $Rularjctvtt; ?></td>
                        <td><?= $Rularjctvtpt; ?></td>
                        </tr>

                        <!-- Row 4 -->
                        <tr>
                        <td rowspan="3">ከተማ</td>
                        <td>ወንድ</td>
                        <td><?= $report['urban_m_tvt']; ?></td>
                        <td><?= $report['urban_m_ajstvt']; ?></td>
                        <td><?= $report['urban_m_ajctvtp']; ?></td>
                        <td><?= $report['urban_m_ajctvtt']; ?></td>
                        <td><?= $Urbanjctvtmpt; ?></td>
                        </tr>

                        <!-- Row 5 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['urban_f_tvt']; ?></td>
                        <td><?= $report['urban_f_ajstvt']; ?></td>
                        <td><?= $report['urban_f_ajctvtp']; ?></td>
                        <td><?= $report['urban_f_ajctvtt']; ?></td>
                        <td><?= $Urbanjctvtfpt; ?></td>
                        </tr>

                        <!-- Row 6 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalUrbantvt; ?></td>
                        <td><?= $totalUrbanajstvt; ?></td>
                        <td><?= $Urbanjctvtp; ?></td>
                        <td><?= $Urbanjctvtt; ?></td>
                        <td><?= $Urbanjctvtpt; ?></td>
                        </tr>

                        <!-- Row 7 -->
                        <tr>
                        <td rowspan="3">ከተማ እና ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $totalMaletvt; ?></td>
                        <td><?= $totalMaleajstvt; ?></td>
                        <td><?= $URjctvtmp; ?></td>
                        <td><?= $URjctvtmt; ?></td>
                        <td><?= $URjctvtmpt; ?></td>
                        </tr>

                        <!-- Row 8 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $totalFemaletvt; ?></td>
                        <td><?= $totalFemaleajstvt; ?></td>
                        <td><?= $URjctvtfp; ?></td>
                        <td><?= $URjctvtft; ?></td>
                        <td><?= $URjctvtfpt; ?></td>
                        </tr>

                        <!-- Row 9 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $grandTotaltvt; ?></td>
                        <td><?= $grandTotalajstvt; ?></td>
                        <td><?=$URjctvtp; ?></td>
                        <td><?= $URjctvtt; ?></td>
                        <td><?= $URjctvtpt; ?></td>
                        </tr>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>