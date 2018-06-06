<?php 
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	include_once $_SERVER['DOCUMENT_ROOT'] . '/path.php';
	include_once ROOT . 'parameter.php';
	include_once MODULE_FUNCTION . 'QueryCheck.php';
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	class SELECT
	{
		private $Set;
		private $QueryCheck;
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		public function __cunstruct ($Connection)
		{
			$this->Set = $Connection;				
			$this->QueryCheck = new QUERY_CHECK($Connection);
		}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		public function MakeSubWhereString($WhereArray , $TableName , $Clause)
		{
			$WhereArray = json_decode($WhereArray, TRUE);
			foreach ($WhereArray as $ColumnName) 
			{
				if($this->QueryCheck->IsColumnExist($TableName , $ColumnName))
				{
					$ColumnName = json_decode($ColumnName , TRUE);
					if(isset($ColumnName['Operator']) && isset($ColumnName['ClauseValue']))
					{
						$Operator = $ColumnName['Operator'];
						$ClauseValue = $ColumnName['ClauseValue'];
						if($Operator == '>' || $Operator == '=' || $Operator == '<' || $Operator == '>=' || $Operator == '<=' || $Operator == 'LIKE')
						{
							$WhereConditionsArray[$ColumnName] = $ColumnName . $Operator . ':' . $ClauseValue . ' ' . $Clause . ' ';
						}
					}
				}
			}
//-----------------------------------------------------------------------------------------------------------------------------------
			foreach ($WhereConditionsArray as $ColumnName) 
			{
				$WhereString .= $WhereConditionsArray[$ColumnName]
			}
			if($WhereString != NULL)
			{
				$WhereString = rtrim($WhereString , ' ' . $Clause . ' ');
				$WhereString = '( ' . $WhereString . ' )';
			}
			return $WhereString;
		}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		public function MakeWhereString($WhereString_AND = NULL , $WhereString_OR = NULL , $Clause)
		{
			$WhereString = $WhereString_AND . ' ' . $Clause . ' ' . $WhereString_OR;				
    		if(!$WhereString_AND && !$WhereString_OR)
    		{
				$WhereString = FALSE;
    		}
    		if($WhereString_AND && !$WhereString_OR)
    		{
    			$WhereString = rtrim($WhereString , ' ' . $Clause . ' ');
    		}
    		if(!$WhereString_AND && $WhereString_OR)
    		{
    			$WhereString = ltrim($WhereString , ' ' . $Clause . ' ');
    		}
    		return $WhereString;
		}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		public function MakeOrderByString($OrderByArray , $TableName)
		{
			$ColumnName_DESC = NULL;
			$ColumnName_ASC = NULL;
//-----------------------------------------------------------------------------------------------------------------------------------
			$OrderByArray = json_decode($OrderByArray , TRUE);
			foreach ($OrderByArray as $ColumnName) 
			{
				if($this->QueryCheck->IsColumnExist($TableName , $ColumnName))
				{
					$ColumnName = json_decode($ColumnName , TRUE);
					if(isset($ColumnName['Option']))
					{
						$Option = $ColumnName['Option'];
							if($Option == 'ASC' && $Option == 'DESC' && $Option == '')
							{
								switch ($Option) 
								{
									case 'DESC':
										$ColumnName_DESC .= $ColumnName . ' , ';
										break;
									case '':
									case 'ASC':
										$ColumnName_ASC .= $ColumnName . ' , ';
										break;
								}
							}
						}
					}
				}
			}
//-----------------------------------------------------------------------------------------------------------------------------------
			if($ColumnName_DESC != NULL)
			{
				$ColumnName_DESC = rtrim($ColumnName_DESC , ' , ');
				$ColumnName_DESC .= ' DESC';
			}
			if($ColumnName_ASC != NULL)
			{
				$ColumnName_ASC = rtrim($ColumnName_ASC , ' , ');
				$ColumnName_ASC .= ' ASC';
			}
			$OrderByString = $ColumnName_DESC . ' , ' . $ColumnName_ASC;
//-----------------------------------------------------------------------------------------------------------------------------------
			if(!$ColumnName_DESC && !$ColumnName_ASC)
    		{
				$OrderByString = FALSE;
    		}
    		if($ColumnName_DESC && !$ColumnName_ASC)
    		{
    			$OrderByString = rtrim($OrderByString , ' , ');
    		}
    		if(!$ColumnName_DESC && $ColumnName_ASC)
    		{
    			$OrderByString = ltrim($OrderByString , ' , ');
    		}
//-----------------------------------------------------------------------------------------------------------------------------------
			return $OrderByString;
		}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		public function MakeAggregateFunctionString($TableName , $ColumnName , $FunctionName)
		{
			if($QueryCheck->IsColumnExist($TableName , $ColumnName))
			{
				$FunctionName = strtoupper($FunctionName);
				$AggregateFunctionString = $FunctionName . '(' . $ColumnName . ')';
			}
			else
			{
				$AggregateFunctionString = FALSE;
			}
			return $AggregateFunctionString;
		}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%		
		public function MakeColumnString($ColumnsArray , $TableName)
		{
			$ColumnString = NULL;
			foreach ($ColumnsArray as $ColumnName) 
			{
				if($QueryCheck->IsColumnExist($TableName , $ColumnName))
				{
					$ColumnString .= $ColumnName . ' , ';
				}
				else
				{
					return FALSE;
				}
			}
			if($ColumnString != NULL)
			{
				$ColumnString = rtrim($ColumnString , ' , ');
				return $ColumnString;
			}
			else
			{
				return FALSE;
			}
		}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		public function MakeParamsArray($TableName , $WhereArray_AND = NULL , $WhereArray_OR = NULL)
		{
			$ParamsArray = array();		
//-----------------------------------------------------------------------------------------------------------------------------------
			if($WhereArray_AND != NULL)
			{			
				$WhereArray_AND = json_decode($WhereString_AND , TRUE);
				foreach ($WhereArray_AND as $ColumnName) 
				{
					if($this->QueryCheck->IsColumnExist($TableName , $ColumnName))
					{
						$Columns = json_decode($ColumnName , TRUE);
						if(!isset($ParamsArray[$ColumnName]))
						{
							$ParamsArray[$ColumnName] = $Columns[$ColumnName]; 
						}
					}
				}
			}
//-----------------------------------------------------------------------------------------------------------------------------------
			if($WhereArray_OR != NULL)
			{
				$WhereArray_OR = json_decode($WhereArray_OR , TRUE);
				foreach ($WhereArray_OR as $ColumnName) 
				{
					if($this->QueryCheck->IsColumnExist($TableName , $ColumnName))
					{
						$Columns = json_decode($ColumnName , TRUE);
						if(!isset($ParamsArray[$ColumnName]))
						{
							$ParamsArray[$ColumnName] = $Columns[$ColumnName]; 
						}
					}
				}
			}
//-----------------------------------------------------------------------------------------------------------------------------------
			if($WhereArray_AND == NULL && $WhereArray_OR == NULL)
			{
				return FALSE;
			}
			return $ParamsArray;
		}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		public function SetData($TableName , $ColumnListString , $WhereString , $GroupByString , $OrderByString , $LimitNumberRows , $AggregateFunctionArray , $Params)
		{
			$SetDataArray = array(
									'TableName'					=> $TableName,
									'ColumnListString'			=> $ColumnListString,
									'WhereString'				=> $WhereString,
									'GroupByString'				=> $GroupByString,
									'OrderByString'				=> $OrderByString,
									'LimitNumberRows'			=> $LimitNumberRows,
									'AggregateFunctionArray'	=> $AggregateFunctionArray,
									'Params'					=> $Params
								);
			return $SetDataArray;
		}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		public function MakeQuery($SetDataArray)
		{
			$SelectString 	= 'SELECT ' . $SetDataArray['ColumnListString'];
//-----------------------------------------------------------------------------------------------------------------------------------			
			$FromString = 'FROM `' . $SetDataArray['TableName'] . '`';			
//-----------------------------------------------------------------------------------------------------------------------------------			
			$WhereString 	= 'WHERE ' . $SetDataArray['WhereString'];
//-----------------------------------------------------------------------------------------------------------------------------------
			if($SetDataArray['OrderByString'] != NULL)
			{
				$OrderByString 	= 'ORDER BY ' . $SetDataArray['OrderByString'];
			}
			else
			{
				$OrderByString = NULL;
			}
//-----------------------------------------------------------------------------------------------------------------------------------
			if($SetDataArray['GroupByString'] != NULL)
			{
				$GroupByString 	= 'GROUP BY ' . $SetDataArray['GroupByString'];
			}
			else
			{
				$GroupByString = NULL;
			}			
//-----------------------------------------------------------------------------------------------------------------------------------
			if($SetDataArray['LimitNumberRows'] != NULL)
			{
				$LimitString	= 'LIMIT ' . $SetDataArray['LimitNumberRows'];
			}
			else
			{
				$LimitString = NULL;
			}
//-----------------------------------------------------------------------------------------------------------------------------------
			if(isset($SetDataArray['AggregateFunctionArray']))
			{
				foreach ($SetDataArray['AggregateFunctionArray'] as $FunctionString) 
				{
					if(isset($FunctionString))
					{
						$AggregateFunctionString .= $FunctionString . ' , ';
					}
				}
				$AggregateFunctionString = rtrim($AggregateFunctionString , ' , ');
				$AggregateFunctionString = ' , ' . $AggregateFunctionString;
			}
			else
			{
				$AggregateFunctionString = NULL;
			}
//-----------------------------------------------------------------------------------------------------------------------------------
			$SelectQuery = '"' . $SelectString . ' ' . $AggregateFunctionString . ' ' . $FromString . ' ' . $WhereString . ' ' . $OrderByString . ' ' . $GroupByString . ' ' . $LimitString . '"';
			return $SelectQuery;
		}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		public function ExecuteData($SelectQuery , $SetDataArray)
		{
			$Temp = $this->Set->prepare($SelectQuery);
			$Result = $Temp->execute($SetDataArray['Params']);
			if($Result)
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
?>