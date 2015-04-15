<?php

/**
 * @package    BitrixAdapter
 *
 * @copyright
 */

class BitrixAdapterDb
{

  /**
   * Convert CDBResult to array.
   *
   */
   
  public static function CDBResultToArray(CDBResult $CDBResult)
  {
    if ($CDBResult->SelectedRowsCount() == 0) return array();
    $ret = array();
    while ($row = $CDBResult->Fetch())
    {
      $ret[] = $row;
    }
    return $ret;
  }
  
}