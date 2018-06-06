<?php
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	include_once $_SERVER['DOCUMENT_ROOT'] . '/path.php';
	include_once ROOT . 'parameter.php';
	include_once MODULE_CLASS . 'Update.php';
	include_once MODULE_FUNCTION . 'QueryCheck.php';
	include_once MODULE_CLASS . 'DatabaseManagement.php';
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	$DatabaseManagement = new DATABASE_MANAGEMENT();
	$Connection = $DatabaseManagement->getConnection();
	$Update = new UPDATE($Connection);
	$QueryCheck = new QUERY_CHECK($Connection); 
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function ERROR($ErrorStr)
	{
		echo "ERROR : " . $ErrorStr . " !";
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	function SUCCESS()
	{
		echo "SUCCESS";
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
/* 
	Posted Values From Ajax :
	TableName 		=> A Value 	: TableName:?
	SetArray 		=> Array 	: SetArray:[ColumnName:NewValue]
	WhereArray_AND 	=> Array 	: WhereArrayAnd:[ColumnName:[Operator:? , ClauseValue:?]]
	WhereArray_OR 	=> Array 	: WhereArrayOR:[ColumnName:[Operator:? , ClauseValue:?]]
	OrderBy 		=> Array 	: OrderBy:[ColumnName:[Option:?(DESC,ASC)]]
	LimitNumberRows => An Int 	: LimitNumberRows:?
*/
//-----------------------------------------------------------------------------------------------------------------------------------
	$TableName 			= NULL;
	$SetArray			= NULL;
	$WhereArray_AND 	= NULL;
	$WhereArray_OR 		= NULL;
	$OrderByString 		= NULL;
	$LimitNumberRows	= NULL;
//-----------------------------------------------------------------------------------------------------------------------------------
	if(isset($_POST['TableName']))
	{
		$TableName = $_POST['TableName'];
		if($QueryCheck->IsTableExist($TableName))
		{
			$Params = $Update->MakeParamsArray($TableName , $WhereArray_AND , $WhereArray_OR , $SetArray);
			if($Params)
			{
				$Columns = $QueryCheck->GetColumnsList($TableName);
				if($Columns == FALSE)
				{
					ERROR('The ' . $TableName . 'has not any column');
					return;
				}
				else
				{
					if(isset($_POST['SetArray']))
					{
						$SetString = $Update->MakeSetString($_POST['SetArray'] , $TableName);
					}
					else
					{
						ERROR('Any set array is not post from form');
						return;
					}
//-----------------------------------------------------------------------------------------------------------------------------------
					if(isset($_POST['WhereArray_AND']))
					{
						$WhereString_AND = $Update->MakeSubWhereString($_POST['WhereArray_AND'] , $TableName , 'AND');
					}
					if(isset($_POST['WhereArray_OR']))
					{
						$WhereString_OR = $Update->MakeSubWhereString($_POST['WhereArray_OR'] , $TableName , 'OR');
					}	
					$WhereString = $Update->MakeWhereString($WhereString_AND , $WhereString_OR , 'AND');
					if(!$WhereString)
					{
						ERROR('Any condition is not posted from form');
						return;
					}
//-----------------------------------------------------------------------------------------------------------------------------------
    				if(isset($_POST['OrderBy']))
    				{
    					$OrderByString = $Update->MakeOrderByString($_POST['OrderBy'] , $TableName);
    				}
//-----------------------------------------------------------------------------------------------------------------------------------
    				if(isset($_POST['LimitNumberRows']))
    				{
    					$MaxNumberOfRows = $QueryCheck->GetNumberOfRows($TableName);
    					if(is_integer($_POST['LimitNumberRows']) && $_POST['LimitNumberRows'] <= $MaxNumberOfRows)
    					{
    						$LimitNumberRows = $_POST['LimitNumberRows'];
    					}
    				}
//-----------------------------------------------------------------------------------------------------------------------------------
    			}
				$SetData 	= $Update->SetData($TableName , $SetString , $WhereString , $OrderByString , $LimitNumberRows , $Params);
				$MakeQuery 	= $Update->MakeQuery($SetData);
				$Result 	= $Update->ExecuteData($MakeQuery , $SetData);
				if($Result)
				{
					SUCCESS();
					return;
				}
				else
				{
					ERROR('Cannot Update ' . $TableName . '`s data');
					return;
				}
			}
			else
			{
				ERROR('Columns value not set from form');
				return;
			}
		}
		else
		{
			ERROR('The Table name : "' . $TableName . '"  does not exist');
			return;
		}				
	}
	else
	{
		ERROR('None table name is posted from form');
		return;		
	}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
?>