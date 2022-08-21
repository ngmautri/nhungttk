<?php
namespace HR\Domain\Employee\Definition;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class IndividualDefinition
{

    public static $fields = [
        /*
         * |=============================
         * | Application\Entity\HrIndividual
         * |
         * |=============================
         */

        "id",
        "individualType",
        "individualName",
        "individualNameLocal",
        "firstName",
        "firstNameLocal",
        "middleName",
        "middleNameLocal",
        "lastName",
        "lastNameLocal",
        "nickName",
        "gender",
        "birthday",
        "lastStatusId",
        "createdOn",
        "lastChangeOn",
        "remarks",
        "employeeStatus",
        "employeeCode",
        "revisionNo",
        "version",
        "sysNumber",
        "token",
        "uuid",
        "passportNo",
        "passportIssuePlace",
        "passportIssueDate",
        "passportExpiredDate",
        "workPermitNo",
        "workPermitDate",
        "workPermitExpiredDate",
        "familyBookNo",
        "ssoNumber",
        "personalIdNumber",
        "personalIdNumberDate",
        "personalIdNumberExpiredDate",
        "createdBy",
        "lastChangeBy",
        "company",
        "nationality"
    ];

    public static $defaultExcludedFields = [
        "firtName",
        "middleName",
        "lastName",
        "birthday",
        "remarks"
    ];

    public static $defaultIncludedFields = [
        "id",
        "token",
        "createdBy",
        "createdOn",
        "lastChangeOn",
        "lastChangeBy",
        "sysNumber",
        "company",
        "individualType",
        "revisionNo"
    ];
}
