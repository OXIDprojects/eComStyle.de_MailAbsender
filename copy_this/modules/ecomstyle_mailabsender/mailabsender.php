<?php

class mailabsender extends mailabsender_parent
{ 
 
    public function sendOrderEmailToOwner( $oOrder, $sSubject = null )
    {
        $myConfig = $this->getConfig();

        $oShop = $this->_getShop();

        // cleanup
        $this->_clearMailer();

        // add user defined stuff if there is any
        $oOrder = $this->_addUserInfoOrderEMail( $oOrder );

        $oUser = $oOrder->getOrderUser();
        $this->setUser( $oUser );

        // send confirmation to shop owner
        // send not pretending from order user, as different email domain rise spam filters
       //ORGINAL: $this->setFrom( $oShop->oxshops__oxowneremail->value );
       //START: 
        $sFullName = $oUser->oxuser__oxfname->getRawValue() . " " . $oUser->oxuser__oxlname->getRawValue(); 
        $this->setFrom( $oUser->oxuser__oxusername->value, $sFullName ); 
       //END
        $oLang = oxRegistry::getLang();
        $iOrderLang = $oLang->getObjectTplLanguage();

        // if running shop language is different from admin lang. set in config
        // we have to load shop in config language
        if ( $oShop->getLanguage() != $iOrderLang ) {
            $oShop = $this->_getShop( $iOrderLang );
        }

        $this->setSmtp( $oShop );

        // create messages
        $oSmarty = $this->_getSmarty();
        $this->setViewData( "order", $oOrder );

        // Process view data array through oxoutput processor
        $this->_processViewArray();

        $this->setBody( $oSmarty->fetch( $myConfig->getTemplatePath( $this->_sOrderOwnerTemplate, false ) ) );
        $this->setAltBody( $oSmarty->fetch( $myConfig->getTemplatePath( $this->_sOrderOwnerPlainTemplate, false ) ) );

        //Sets subject to email
        // #586A
        if ( $sSubject === null ) {
            if ( $oSmarty->template_exists( $this->_sOrderOwnerSubjectTemplate) ) {
                $sSubject = $oSmarty->fetch( $this->_sOrderOwnerSubjectTemplate );
            } else {
                 $sSubject = $oShop->oxshops__oxordersubject->getRawValue()." (#".$oOrder->oxorder__oxordernr->value.")";
            }
        }

        $this->setSubject( $sSubject );
        $this->setRecipient( $oShop->oxshops__oxowneremail->value, $oLang->translateString("order") );

        if ( $oUser->oxuser__oxusername->value != "admin" ) {
            $sFullName = $oUser->oxuser__oxfname->getRawValue() . " " . $oUser->oxuser__oxlname->getRawValue();
            $this->setReplyTo( $oUser->oxuser__oxusername->value, $sFullName );
        }

        $blSuccess = $this->send();

        // add user history
        $oRemark = oxNew( "oxremark" );
        $oRemark->oxremark__oxtext      = new oxField($this->getAltBody(), oxField::T_RAW);
        $oRemark->oxremark__oxparentid  = new oxField($oUser->getId(), oxField::T_RAW);
        $oRemark->oxremark__oxtype      = new oxField("o", oxField::T_RAW);
        $oRemark->save();


        if ( $myConfig->getConfigParam( 'iDebug' ) == 6) {
            oxRegistry::getUtils()->showMessageAndExit( "" );
        }

        return $blSuccess;
    }
    
}    