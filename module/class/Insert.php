<?php 
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	include_once $_SERVER['DOCUMENT_ROOT'] . '/path.php';
	include_once ROOT . 'parameter.php';
	include_once MODULE_FUNCTION . 'QueryCheck.php';
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	class INSERT
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
		public function MakeParamsArray($TableName , $ColumnsArray = NULL)
		{
			$ParamsArray = array();
			if($ColumnsArray != NULL)
			{			
				$ColumnsArray = json_decode($ColumnsArray , TRUE);
				foreach ($ColumnsArray as $ColumnName => $ColumnValue) 
				{
					if($this->QueryCheck->IsColumnExist($TableName , $ColumnName))
					{
						$ParamsArray[$ColumnName] = $ColumnValue; 
					}
				}
			}
			else
			{
				return FALSE;
			}
			return $ParamsArray;
		}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		public function MakeColumnString($ColumnsArray , $TableName , $Type)
		{
			if($Type == 'Name')
			{
				$Bind = '';
			}
			else
			{
				$Bind = ':'
			}
			if($ColumnsArray != NULL)
			{
				$ColumnString = NULL;		
				$ColumnsArray = json_decode($ColumnsArray , TRUE);
				foreach ($ColumnsArray as $ColumnName) 
				{
					if($this->QueryCheck->IsColumnExist($TableName , $ColumnName))
					{
						$ColumnString .= $Bind . $ColumnName . ' , ';
					}
				}
				if($ColumnString != NULL)
				{
					$ColumnString = rtrim($ColumnString , ' , ');
				}
			}	
			else
			{
				$ColumnString = FALSE;
			}
			return $ColumnString;				
		}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		public function SetData($TableName , $ColumnNameString , $ColumnValueString , $Params)
		{
			$SetDataArray = array(
									'TableName'			=> $TableName,
									'ColumnNameString' 	=> $ColumnNameString,
									'ColumnValueString' => $ColumnValueString,
									'Params'			=> $Params
								);
			return $SetDataArray;
		}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		public function MakeQuery($SetDataArray)
		{
			$InsertString 	= 'INSERT INTO `' . $SetDataArray['TableName'] 	. '`';
//-----------------------------------------------------------------------------------------------------------------------------------			
			$ColumnNameString = '( ' . $SetDataArray['ColumnNameString'] . ' )';
//-----------------------------------------------------------------------------------------------------------------------------------			
			$ColumnValueString = 'VALUES ( ' . $SetDataArray['ColumnValueString'] . ' )';
//-----------------------------------------------------------------------------------------------------------------------------------
			$InsertQuery = '"' . $InsertString . ' ' . $ColumnNameString . ' ' . $ColumnValueString . '"';
			return $InsertQuery;
		}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		public function ExecuteData($InsertQuery , $SetDataArray)
		{
			$Temp = $this->Set->prepare($InsertQuery);
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