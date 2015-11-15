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
      isset($row['ID']) ? $ref =& $ret[$row['ID']] : $ref =& $ret[];
      $ref = $row;
    }
    return $ret;
  }
  
}
