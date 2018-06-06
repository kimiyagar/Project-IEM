<?php
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	include_once $_SERVER['DOCUMENT_ROOT'] . '/path.php';
	include_once ROOT . 'parameter.php';
	include_once MODULE_CLASS . 'Insert.php';
	include_once MODULE_FUNCTION . 'QueryCheck.php';
	include_once MODULE_CLASS . 'DatabaseManagement.php';
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	$DatabaseManagement = new DATABASE_MANAGEMENT();
	$Connection = $DatabaseManagement->getConnection();
	$Insert = new INSERT($Connection);
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
	TableName 			=> A Value 	: TableName:?
	ColumnsArray 		=> Array 	: InsertArray:[ColumnName:ColumnValue]
*/
//-----------------------------------------------------------------------------------------------------------------------------------
	$TableName 			= NULL;
	$ColumnsArray		= NULL;
//-----------------------------------------------------------------------------------------------------------------------------------
	if(isset($_POST['TableName']))
	{
		$TableName = $_POST['TableName'];
		if($QueryCheck->IsTableExist($TableName))
		{
			$Params = $Insert->MakeParamsArray($TableName , $ColumnsArray);
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
    				if(isset($_POST['ColumnsArray']))
    				{
    					$MakeColumnNameString = $Insert->MakeColumnString($_POST['ColumnsArray'] , $TableName , 'Name');
						$MakeColumnValueString = $Insert->MakeColumnString($_POST['ColumnsArray'] , $TableName , 'Value');
    				}
//-----------------------------------------------------------------------------------------------------------------------------------
    			}
				$SetData 	= $Insert->SetData($TableName , $ColumnNameString , $ColumnValueString , $Params);
				$MakeQuery 	= $Insert->MakeQuery($SetData);
				$Result 	= $Insert->ExecuteData($MakeQuery , $SetData);
				if($Result)
				{
					SUCCESS();
					return;
				}
				else
				{
					ERROR('Cannot Insert ' . $TableName . '`s data');
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