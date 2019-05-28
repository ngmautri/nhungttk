<?php
namespace Inventory\Domain\Warehouse\Transaction;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class TransactionType{
    
    const GI_FOR_REPAIR_MACHINE = 'GI099';
    
    const GI_FOR_REPAIR_MACHINE_WITH_EX = 'GI100';
    
    const GI_FOR_PROJECT = 'GI101';
    
    const GI_FOR_COST_CENTER = 'GI102';
    
    const GI_FOR_PRODUCTION_ORDER = 'GI103';
    
    const GI_FOR_RETURN_PO = 'GI104';
    
    const GI_FOR_REPAIRING = 'GI105';
    
    const GI_FOR_MAINTENANCE_WORK = 'GI106';
    
    const GI_FOR_ASSET = 'GI107';
    
    const GI_FOR_TRANSFER_WAREHOUSE = 'GI108';
    
    const GI_FOR_TRANSFER_LOCATION = 'GI109';
    
    const GI_FOR_DISPOSAL = 'GI999';
    
    // ========================================
    const GR_FROM_OPENNING_BALANCE = 'GR000';
    
    const GR_FROM_PURCHASING = 'GR100';
    
    const GR_FROM_TRANSFER_WAREHOUSE = 'GR101';
    
    const GR_FROM_TRANSFER_LOCATION = 'GR102';
    
    const GR_FROM_PURCHASING_REVERSAL = 'GR100-1';
    
}