<?php

/**
 * Created by PhpStorm.
 * User: Ubel
 * Date: 22/02/2019
 * Time: 18:34
 */

namespace App;

/**
 * Undocumented class
 */
abstract class constants
{
    //---------BEGIN---------//
    //-----notifications-----//
    const NOTIFICATION_PROFILE_UPDATE       =   "update_profile";
    const NOTIFICATION_JOB_CREATE           =   "create_profile";
    const NOTIFICATION_JOB_UPDATE           =   "update_job";
    const NOTIFICATION_PAYMENT_SUCCESS      =   "payment_success";
    const NOTIFICATION_QUERY_CREATE         =   "query_create";
    //-----notifications-----//
    //----------END----------//

    //---------BEGIN---------//
    //--------metadata-------//
    const  METADATA_SKILL_DAO               =   "metadata_skill";
    const  METADATA_EDUCATION_DAO           =   "metadata_education";
    const  METADATA_EXPERIENCE_DAO          =   "metadata_experience";
    const  METADATA_PORCENT_DAO             =   "metadata_porcent";
    const  METADATA_QUALIFICATION_DAO       =   "metadata_qualification";
    //--------metadata-------//
    //----------END----------//

    //---------BEGIN---------//
    //---------Resume--------//
    const  METADATA_RESUME_EDIT             =   "resume_edit";
    //---------Resume--------//
    //----------END----------//

    //---------BEGIN---------//
    //--------Applied--------//
    const  NOTIFICATIONS_JOB_APPLIED_OK     =   "job_applied_ok";
    const  NOTIFICATIONS_JOB_APPLIED_CANCEL =   "job_applied_cancel";
    const  NOTIFICATIONS_JOB_APPLIED_ADMIN  =   "job_applied_admin";
    //--------Applied--------//
    //----------END----------//

    //---------BEGIN---------//
    //--------Payment--------//
    const  PAYMENT_FREE                     =   "payment_free";
    const  PAYMENT_FREE_DAYS                =   15;
    const  PAYMENT_BASIC                    =   "payment_basic";
    const  PAYMENT_BASIC_DAYS               =   30;
    const  PAYMENT_PYME                     =   "payment_pyme";
    const  PAYMENT_PYME_DAYS                =   30;
    const  PAYMENT_PLUS                     =   "payment_plus";
    const  PAYMENT_PLUS_DAYS                =   45;
    const  PAYMENT_METHOD_CREDIT_CARD       =   "payment_method_credit_card";
    const  PAYMENT_METHOD_PAYPAL            =   "payment_method_paypal";
    //--------Payment--------//
    //----------END----------//

    //---------BEGIN---------//
    //-------job_status------//
    const  JOB_STATUS_ACTIVE                 =  "job_active";
    const  JOB_STATUS_PENDING                =  "job_pending";
    const  JOB_STATUS_EXPIRED                =  "job_expired";
    const  JOB_STATUS_LOOCK                  =  "job_loock";
    //--------job_status--------//
    //----------END----------//

}