<?php
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	include_once $_SERVER['DOCUMENT_ROOT'] . '/path.php';
	include_once ROOT . 'parameter.php';
	include_once MODULE_CLASS . 'Delete.php';
	include_once MODULE_FUNCTION . 'QueryCheck.php';
	include_once MODULE_CLASS . 'DatabaseManagement.php';
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	$DatabaseManagement = new DATABASE_MANAGEMENT();
	$Connection = $DatabaseManagement->getConnection();
	$Delete = new DELETE($Connection);
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
	WhereArray_AND 	=> Array 	: WhereArrayAnd:[ColumnName:[Operator:? , ClauseValue:?]]
	WhereArray_OR 	=> Array 	: WhereArrayOR:[ColumnName:[Operator:? , ClauseValue:?]]
	OrderBy 		=> Array 	: OrderBy:[ColumnName:[Option:?(DESC,ASC)]]
	LimitNumberRows => An Int 	: LimitNumberRows:?
*/
//-----------------------------------------------------------------------------------------------------------------------------------
	$TableName 			= NULL;
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
			$Params = $Delete->MakeParamsArray($TableName , $WhereArray_AND , $WhereArray_OR);
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
//-----------------------------------------------------------------------------------------------------------------------------------
					if(isset($_POST['WhereArray_AND']))
					{
						$WhereString_AND = $Delete->MakeSubWhereString($_POST['WhereArray_AND'] , $TableName , 'AND');
					}
					if(isset($_POST['WhereArray_OR']))
					{
						$WhereString_OR = $Delete->MakeSubWhereString($_POST['WhereArray_OR'] , $TableName , 'OR');
					}	
					$WhereString = $Delete->MakeWhereString($WhereString_AND , $WhereString_OR , 'AND');
					if(!$WhereString)
					{
						ERROR('Any condition is not posted from form');
						return;
					}
//-----------------------------------------------------------------------------------------------------------------------------------
    				if(isset($_POST['OrderBy']))
    				{
    					$OrderByString = $Delete->MakeOrderByString($_POST['OrderBy'] , $TableName);
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
				$SetData 	= $Delete->SetData($TableName , $WhereString , $OrderByString , $LimitNumberRows , $Params);
				$MakeQuery 	= $Delete->MakeQuery($SetData);
				$Result 	= $Delete->ExecuteData($MakeQuery , $SetData);
				if($Result)
				{
					SUCCESS();
					return;
				}
				else
				{
					ERROR('Cannot delete ' . $TableName . '`s data');
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