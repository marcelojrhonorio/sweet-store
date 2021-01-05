<?php

namespace App\Traits;

trait FixMailDomain
{
    use ArraySome;

    private $blockedCalogaEmails = [
        '/hotmail\.com$/',
        '/live\.com$/',
        '/msn\.com$/',
        '/outlook\.com$/',

        // Domínios bloqueados 05/11/2019
        '/axkleinfa\.com$/',
        '/kluofficer\.com$/',
        '/openskj\.com$/',
        '/hroundb\.com$/',
        '/xplanningzx\.com$/',
        '/billionvj\.com$/',
        '/8i7\.net$/',
        '/mails-24\.net$/',
        '/mail-v\.net$/',
        '/amailr\.net$/',
        '/mail-fix\.net$/',
        '/mailspro\.net$/',
        '/itmailr\.com$/',
        '/webhomes\.net$/',
        '/mailr24\.com$/',
        '/ruru\.be$/',
        '/kbox\.li$/',
        '/honeys\.be$/',
        '/f5\.si$/',
        '/cream\.pink$/',
        '/mbox\.re$/',
        '/risu\.be$/',
        '/ponp\.be$/',
        '/usako\.net$/',
        '/merry\.pink$/',
        '/neko2\.net$/',
        '/fuwa\.be$/',
        '/kpay\.be$/',
        '/kksm\.be$/',
        '/rapt\.be$/',
        '/prin\.be$/',
        '/fuwamofu\.com$/',
        '/kmail\.li$/',
        '/choco\.la$/',
        '/mailnow2\.com$/',
        '/enayu\.com$/',
        '/tmpmail\.org$/',
        '/w6mail\.com$/',
        '/moakt\.ws$/',
        '/crazymail\.guru$/',
        '/hellomail\.fun$/',
        '/wimsg\.com$/',
        '/vmani\.com$/',
        '/emailna\.co$/',
        '/mail-file\.net$/',
        '/88av\.net$/',
        '/spambox\.me$/',
        '/rev-mail\.net$/',
        '/dr-mail\.net$/',
        '/winnweb\.net$/',
        '/mail-lab\.net$/',
        '/mail-line\.net$/',
        '/mailfile\.net$/',
        '/drmail\.net$/',
        '/mail-guru\.net$/',
        '/mail-desk\.net$/',
        '/mail-search\.com$/',
        '/rich-mail\.net$/',
        '/dot-coin\.com$/',
        '/be-breathtaking\.net$/',
        '/getbreathtaking\.com$/',
        '/247web\.net$/',
        '/web-inc\.net$/',
        '/temp-link\.net$/',
        '/free-temp\.net$/',
        '/4nextmail\.com$/',
        '/net1mail\.com$/',
        '/mail-card\.net$/',
        '/itfast\.net$/',
        '/tympe\.net$/',
        '/bit-ion\.net$/',
        '/ichigo\.me$/',
        '/mailna\.biz$/',
        '/tmail\.ws$/',
        '/disbox\.net$/',
        '/tmpmail\.net$/',
        '/moakt\.cc$/',
        '/znnxguest\.com$/',
        '/mzfactoryy\.com$/',
        '/sburningk\.com$/',
        '/fsmilitary\.com$/',
        '/lendlesssn\.com$/',
        '/flooringuj\.com$/',
        '/ilandingvw\.com$/',
        '/mailsource\.info$/',
        '/eay\.jp$/',
        '/via\.tokyo\.jp$/',
        '/himail\.online$/',
        '/atech5\.com$/',
        '/emlhub\.com$/',
        '/dropmail\.me$/',
        '/themeg\.co$/',
        '/oohotmail\.club$/',
        '/mygoldenmail\.com$/',
        '/mymailboxpro\.org$/',
        '/vteensp\.com$/',
        '/tmails\.net$/',
        '/moakt\.co$/',
        '/rrasianp\.com$/',
        '/bareed\.ws$/',
        '/hostguru\.top$/',
        '/atnextmail\.com$/',
        '/uber-mail\.com$/',
        '/daymail\.life$/',
        '/silvercoin\.life$/',
        '/coinlink\.club$/',
        '/mailnet\.top$/',
        '/netmail8\.com$/',
        '/mailseo\.net$/',
        '/imail8\.net$/',
        '/dmailpro\.net$/',
        '/uetimer\.com$/',
    ];

    private $gmail = [
        '/gmaail\.com$/',
        '/gmael\.com$/',
        '/gmai\.com$/',
        '/gmai\.lcom$/',
        '/gmail\.cim$/',
        '/gmail\.c$/',
        '/gmail\.co\.com$/',
        '/gmail\.com\.br$/',
        '/gemail\.com$/',
        '/gemeio\.com$/',
        '/ggmail\.com$/',
        '/gimail\.com$/',
        '/gmail\.co$/',
        '/gmail\.con$/',
        '/gmail\.coom$/',
        '/gmail\.copm$/',
        '/gmail\.cpm$/',
        '/gmail\.pt\.com$/',
        '/gmailco$/',
        '/gmailcom$/',
        '/gmaill\.com$/',
        '/gmailo\.com$/',
        '/gmal\.com$/',
        '/gmali\.com$/',
        '/gmeil\.com$/',
        '/gmeio$/',
        '/gmial\.com$/',
        '/gmil\.com$/',
        '/gmIl\.com\.br$/',
        '/gml\.com$/',
        '/gmsil\.com$/',
        '/gnail\.com$/',
        '/gmail\.comm$/',
        '/gmail\.comq$/',
        '/gmail\.cm$/',
        '/gmail\.om$/',
        '/gmail\.bcom$/',
        '/3gmail\.com$/',
        '/facanha99gmail\.com$/',
        '/2014gmail\.com$/',
        '/gmaul\.com$/',
        '/gmaol\.com$/',
        '/gmail\.comy$/',
        '/gmail\.como$/',
        '/gmail\.comcom$/',
        '/gmeio\.com$/',
        '/gmal\.com\.br$/',
        '/gmais\.com$/',
        '/gmail\.vom$/',
        '/gmail\.comn$/',
        '/gmail\.coma$/',
        '/gmail\.coms$/',
        '/gmall\.com$/',
        '/gmil\.com\.br$/',
        '/gmmail\.com$/',
        '/gmail\.combr$/',
        '/gamil\.com$/',
    ];

    private $hotmail = [
        '/hogmail\.com$/',
        '/homail\.com$/',
        '/hootmail\.com$/',
        '/hormail\.com$/',
        '/hotamail\.com$/',
        '/hotmai\.com$/',
        '/hotimail\.com$/',
        '/hotmaik\.com$/',
        '/hotmail$/',
        '/hotmail\.ccom$/',
        '/hotmail\.cm$/',
        '/hotmail\.com\.com$/',
        '/hotmail\.con$/',
        '/hotmaill\.com$/',
        '/hotmal\.com$/',
        '/hotmaol\.com$/',
        '/hotmial\.com$/',
        '/hotmmail\.com$/',
        '/hotmsil\.com$/',
        '/hotnail\.com$/',
        '/hotrmail\.com$/',
        '/hotmail\.vom$/',
        '/hotmail\.om$/',
        '/hot\.com$/',
        '/hotmail\.co\.br$/',
        '/hotmat\.com$/',
        '/hotmil\.com$/',
        '/hotma\.com$/',
        '/hotmel\.com$/',
        '/hotmai\.com\.br$/',
        '/hotmali\.com$/',
        '/hotmain\.com$/',
        '/hot\.ail\.com$/',
        '/hotnail\.com\.br$/',
        '/jahot\.mail\.com$/',
        '/hotmal\.com\.br$/',
        '/hotmia\.com$/',
        '/hotmeil\.com$/',
        '/hotmeil\.com\.br$/',
//        '/hotmail\.com\.br$/',
        '/hotmail\.co$/',
        '/hotmael\.com$/',
        '/hotmail\.como$/',
        '/hotmaul\.com$/',
        '/hotmaim\.com$/',
        '/hotmail\.comf$/',
        '/hotimal\.com$/',
        '/hotmaio\.com$/',
        '/hotm\.com$/',
        '/hotimail\.com\.br$/',
        '/hotmall\.com$/',
        '/hotmei\.com$/',
        '/hotmeal\.com$/',
        '/hotmau\.com$/',
        '/hotmail\.c0m$/',
        '/hotmai\.coml$/',
        '/hotmaeil\.com$/',
        '/hotluk\.com$/',
        '/hotlook\.com$/',
        '/hotiml\.com$/',
        '/hotimli\.com$/',
        '/hotimai\.com$/',
        '/hotihotimail\.com$/',
        '/hotemel\.com\.br$/',
        '/hotelipanemasorocaba\.com\.br$/',
        '/hotamiel\.com$/',
        '/hotamal\.com$/',
        '/hot\.comail\.com$/',
        '/123hotmil\.com$/',
        '/18hotmail\.com$/',
        '/2011hotmail\.com$/',
        '/2014hotamil\.com$/',
        '/2hotmail\.com$/',
        '/84hotmsil\.com$/',
        '/atlanticahotels\.com\.br$/',
        '/funphoto\.com\.br$/',
        '/ghot\.com$/',
        '/hotmail\.com\.brakfgpjuisa$/',
        '/hotmail\.com21$/',
        '/hotmail\.com26$/',
        '/hotmail\.coma$/',
        '/hotmail\.comde$/',
        '/hotmail\.comma$/',
        '/hotmail\.comrdadeiro$/',
        '/hotmail\.fom$/',
        '/hotmail\.gomm$/',
        '/hotmailcom\.br$/',
        '/hotmailcom\.com$/',
        '/hotmailha\.com$/',
        '/hotmais\.com$/',
        '/hotmait\.com$/',
        '/hotmal\.con$/',
        '/hotmall\.com\.br$/',
        '/hotmaoil\.com$/',
        '/hotmsil\.com\.br$/',
        '/hotnai\.com$/',
        '/hotpoint\.com\.br$/',
        '/newphoto\.com\.br$/',
        '/photocook\.com\.br$/',
        '/photongroup\.com$/',
        '/towerfrancahotel\.com\.br$/',
        '/xn--hotmai-ncb\.com$/',
    ];

    private $yahoo = [
        '/yahoo\.com$/',
        '/yhaoo\.com\.br$/',
        '/ymail\.com$/',
        '/yhoo\.com\.br$/',
        '/yhooo\.com$/',
        '/yhoo\.com$/',
        '/yanoo\.com$/',
        '/yaoo\.com$/',
        '/yaoo\.com\.br$/',
        '/yaool\.com\.br$/',
        '/yaghoo\.com\.br$/',
        '/yagoo\.com\.br$/',
        '/yahao\.com\.br$/',
        '/yahho\.com\.br$/',
        '/yahhoo\.com\.br$/',
        '/uahoo\.com$/',
        '/uahoo\.com\.br$/',
    ];

    private function isCalogaEmailBlocked($email)
    {
        return $this->arraySome($this->blockedCalogaEmails, function($pattern) use ($email) {
            return preg_match($pattern, $email);
        });
    }

    private function isGmailTypo($email)
    {
        return $this->arraySome($this->gmail, function($pattern) use ($email) {
            return preg_match($pattern, $email);
        });
    }

    private function isHotmailTypo($email)
    {
        return $this->arraySome($this->hotmail, function($pattern) use ($email) {
            return preg_match($pattern, $email);
        });
    }

    private function isYahooTypo($email)
    {
        return $this->arraySome($this->yahoo, function($pattern) use ($email) {
            return preg_match($pattern, $email);
        });
    }

    protected function fixMailDomain($email)
    {
        $isGmailTypo = $this->isGmailTypo($email);

        if ($isGmailTypo) {
            return explode('@', $email)[0] . '@gmail.com';
        }

        $isHotmailTypo = $this->isHotmailTypo($email);

        if ($isHotmailTypo) {
            return explode('@', $email)[0] . '@hotmail.com';
        }

        $isYahooTypo = $this->isYahooTypo($email);

        if ($isYahooTypo) {
            return explode('@', $email)[0] . '@yahoo.com.br';
        }

        return null;
    }
}
