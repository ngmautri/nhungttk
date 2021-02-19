<?php
namespace Procure\Domain\AccountPayable;

use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Procure\Domain\Contracts\ProcureDocType;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class APSnapshotAssembler
{

    private static $defaultExcludedFields = array(
        "id",
        "uuid",
        "token",
        "checksum",
        "createdBy",
        "docType",
        "createdOn",
        "lastChangeOn",
        "lastChangeBy",
        "sysNumber",
        "company",
        "revisionNo",
        "currencyIso3",
        "vendorName",
        "docStatus",
        "workflowStatus",
        "transactionStatus",
        "paymentStatus",
        "currentState",
        "docStatus"
    );

    public static function updateAllFieldsFromArray(APSnapshot $snapShot, $data)
    {
        return GenericObjectAssembler::updateAllFieldsFromArray($snapShot, $data);
    }

    public static function updateIncludedFieldsFromArray(APSnapshot $snapShot, $data, $fields)
    {
        return GenericObjectAssembler::updateIncludedFieldsFromArray($snapShot, $data, $fields);
    }

    public static function updateDefaultIncludedFieldsFromArray(APSnapshot $snapShot, $data)
    {
        return GenericObjectAssembler::updateIncludedFieldsFromArray($snapShot, $data, self::getDefaultIncludedFields($snapShot));
    }

    public static function updateExcludedFieldsFromArray(APSnapshot $snapShot, $data, $fields)
    {
        return GenericObjectAssembler::updateExcludedFieldsFromArray($snapShot, $data, $fields);
    }

    public static function updateDefaultExcludedFieldsFromArray(APSnapshot $snapShot, $data)
    {
        return GenericObjectAssembler::updateIncludedFieldsFromArray($snapShot, $data, self::$defaultExcludedFields);
    }

    // from Object
    // =============================
    public static function updateAllFieldsFrom(APSnapshot $snapShot, $data)
    {
        return GenericObjectAssembler::updateAllFieldsFrom($snapShot, $data);
    }

    public static function updateIncludedFieldsFrom(APSnapshot $snapShot, $data, $fields)
    {
        return GenericObjectAssembler::updateIncludedFieldsFromArray($snapShot, $data, $fields);
    }

    public static function updateDefaultFieldsFrom(APSnapshot $snapShot, $data)
    {
        return GenericObjectAssembler::updateIncludedFieldsFrom($snapShot, $data, self::getDefaultIncludedFields($snapShot));
    }

    public static function updateExcludedFieldsFrom(APSnapshot $snapShot, $data, $fields)
    {
        return GenericObjectAssembler::updateExcludedFieldsFromArray($snapShot, $data, $fields);
    }

    public static function updateDefaultExcludedFieldsFrom(APSnapshot $snapShot, $data)
    {
        return GenericObjectAssembler::updateExcludedFieldsFrom($snapShot, $data, self::$defaultExcludedFields);
    }

    public static function updateEntityExcludedDefaultFieldsFrom(GenericAP $snapShot, $data)
    {
        return GenericObjectAssembler::updateExcludedFieldsFrom($snapShot, $data, self::$defaultExcludedFields);
    }

    public static function updateEntityAllFieldsFrom(GenericAP $snapShot, $data)
    {
        return GenericObjectAssembler::updateAllFieldsFrom($snapShot, $data);
    }

    /**
     *
     * @param APSnapshot $snapShot
     * @return string[]
     */
    private static function getDefaultIncludedFields(APSnapshot $snapShot)
    {
        $result = [
            "isActive",
            "docNumber",
            "docDate",
            "sapDoc",
            "postingDate",
            "contractDate",
            "grDate",
            "remarks",
            "docCurrency",
            "pmtTerm",
            "warehouse",
            "incoterm",
            "incotermPlace",
            "remarks"
        ];

        switch ($snapShot->getDocType()) {

            // ============
            case ProcureDocType::INVOICE:
                // left blank
                break;
            case ProcureDocType::INVOICE_REVERSAL:
                // left blank
                break;
            case ProcureDocType::INVOICE_FROM_PO:

                $result = [
                    "isActive",
                    "docNumber",
                    "docDate",
                    "sapDoc",
                    "postingDate",
                    "grDate",
                    "warehouse",
                    "pmtTerm",
                    "remarks",
                    "docCurrency",
                    "remarks"
                ];
                break;
        }

        return $result;
    }
}
