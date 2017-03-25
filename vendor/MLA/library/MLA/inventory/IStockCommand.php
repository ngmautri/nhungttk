<?php
namespace MLA\inventory;

/**
 * 
 * @author nmt
 *
 */
interface IStockCommand{
	public function issue();
	public function receive();
}