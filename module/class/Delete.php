<?php 
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	include_once $_SERVER['DOCUMENT_ROOT'] . '/path.php';
	include_once ROOT . 'parameter.php';
	include_once MODULE_CLASS . 'QueryCheck.php';
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	class DELETE
	{
		private $Set;
		private $QueryCheck;
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		public function __construct ($Connection)
		{
			$this->Set = $Connection;				
			$this->QueryCheck = new QUERY_CHECK($Connection);
		}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		public function MakeSubWhereString($WhereArray , $TableName , $Clause)
		{
			$WhereString = NULL;
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
				$WhereString .= $WhereConditionsArray[$ColumnName];
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
		public function MakeParamsArray($TableName , $WhereArray_AND = NULL , $WhereArray_OR = NULL)
		{
			$ParamsArray = array();
//-----------------------------------------------------------------------------------------------------------------------------------
			if($WhereArray_AND != NULL)
			{			
				$WhereArray_AND = json_decode($WhereArray_AND , TRUE);
				foreach ($WhereArray_AND as $ColumnName) 
				{
					if($this->QueryCheck->IsColumnExist($TableName , $ColumnName))
					{
						$Columns = json_decode($ColumnName , TRUE);
						$ParamsArray[$ColumnName] = $Columns[$ColumnName]; 
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
		public function SetData($TableName , $WhereString , $OrderByString , $LimitNumberRows , $Params)
		{
			$SetDataArray = array(
									'TableName'			=> $TableName,
									'WhereString' 		=> $WhereString,
									'OrderByString' 	=> $OrderByString,
									'LimitNumberRows' 	=> $LimitNumberRows,
									'Params'			=> $Params
								);
			return $SetDataArray;
		}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		public function MakeQuery($SetDataArray)
		{
			$DeleteString 	= 'DELETE FROM `' . $SetDataArray['TableName'] 	. '`';
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
			if($SetDataArray['LimitNumberRows'] != NULL)
			{
				$LimitString	= 'LIMIT ' . $SetDataArray['LimitNumberRows'];
			}
			else
			{
				$LimitString = NULL;
			}
//-----------------------------------------------------------------------------------------------------------------------------------
			$DeleteQuery = '"' . $DeleteString . ' ' . $WhereString . ' ' . $OrderByString . ' ' . $LimitString . '"';
			return $DeleteQuery;
		}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		public function ExecuteData($DeleteQuery , $SetDataArray)
		{
			$Temp = $this->Set->prepare($DeleteQuery);
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