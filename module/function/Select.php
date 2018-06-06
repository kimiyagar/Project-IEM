<?php
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	include_once $_SERVER['DOCUMENT_ROOT'] . '/path.php';
	include_once ROOT . 'parameter.php';
	include_once MODULE_CLASS . 'Select.php';
	include_once MODULE_FUNCTION . 'QueryCheck.php';
	include_once MODULE_CLASS . 'DatabaseManagement.php';
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	$DatabaseManagement = new DATABASE_MANAGEMENT();
	$Connection = $DatabaseManagement->getConnection();
	$Select = new SELECT($Connection);
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
	TableName   	=> A Value  : TableName:?
	Sum    			=> A Value  : Sum:?	ColumnName
	Max    			=> A Value  : Max:?	ColumnName
	Min    			=> A Value  : Min:?	ColumnName
	Count    		=> A Value  : Count:? ColumnName
	ColumnList 		=> Array : ColumnList:[ColumnName]
	GroupBy			=> Array : GroupBy:[ColumnName]
	WhereArray_AND 	=> Array  : WhereArrayAnd:[ColumnName:[Operator:? , ClauseValue:?]]
	WhereArray_OR  	=> Array  : WhereArrayOR:[ColumnName:[Operator:? , ClauseValue:?]]
	OrderBy   		=> Array  : OrderBy:[ColumnName:[Option:?(DESC,ASC)]]
	LimitNumberRows	=> An Int  : LimitNumberRows:?
*/
//-----------------------------------------------------------------------------------------------------------------------------------
	$TableName   		= NULL;
	$SumString    		= NULL;
	$MaxString    		= NULL;
	$MinString    		= NULL;
	$CountString    	= NULL;
	$ColumnListString 	= NULL;
	$GroupByString		= NULL;
	$WhereArray_AND 	= NULL;
	$WhereArray_OR  	= NULL;
	$OrderByString   	= NULL;
	$LimitNumberRows	= NULL;
	$AggregateFunctionArray = array(
										'SumString' 	=> $SumString,
										'MaxString' 	=> $MaxString,
										'MinString' 	=> $MinString,
										'CountString' 	=> $CountString,
									);
//-----------------------------------------------------------------------------------------------------------------------------------
	if(isset($_POST['TableName']))
	{
  		$TableName = $_POST['TableName'];
  		if($QueryCheck->IsTableExist($TableName))
  		{
			$Params = $Select->MakeParamsArray($TableName , $WhereArray_AND , $WhereArray_OR);
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
					foreach ($AggregateFunctionArray as $FunctionName) 
					{
						switch ($FunctionName) 
						{
							case 'SumString':
								$Function = 'Sum';
								break;
							case 'MaxString':
								$Function = 'Max';
								break;
							case 'MinString':
								$Function = 'Min';
								break;
							case 'CountString':
								$Function = 'Count';
								break;
							default:
								ERROR('Undefine aggregate function name');
								return;
								break;
						}
						if(isset($_POST[$Function]))
						{
							$AggregateFunctionArray[$FunctionName] = $Select->MakeAggregateFunctionString($TableName , $_POST[$Function] , $Function);
							if($AggregateFunctionArray[$FunctionName] == FALSE)
							{
								ERROR('Undefined column name in ' . $FunctionName);
								return;
							}
						}
					}
//-----------------------------------------------------------------------------------------------------------------------------------
					if(isset($_POST['ColumnList']))
					{
						$ColumnListString = $Select->MakeColumnString($_POST['ColumnList'] , $TableName);
					}
					else
					{
						$ColumnListString = "*";
					}
//-----------------------------------------------------------------------------------------------------------------------------------
					if(isset($_POST['GroupBy']))
					{
						$GroupByString = $Select->MakeColumnString($_POST['GroupBy'] , $TableName);
					}
//-----------------------------------------------------------------------------------------------------------------------------------
     				if(isset($_POST['WhereArray_AND']))
     				{
      					$WhereString_AND = $Select->MakeSubWhereString($_POST['WhereArray_AND'] , $TableName , 'AND');
     				}
     				if(isset($_POST['WhereArray_OR']))
     				{
      					$WhereString_OR = $Select->MakeSubWhereString($_POST['WhereArray_OR'] , $TableName , 'OR');
     				} 
     				$WhereString = $Select->MakeWhereString($WhereString_AND , $WhereString_OR , 'AND');
     				if(!$WhereString)
     				{
      					ERROR('Any condition is not posted from form');
      					return;
     				}
//-----------------------------------------------------------------------------------------------------------------------------------
		        	if(isset($_POST['OrderBy']))
        			{
         				$OrderByString = $Select->MakeOrderByString($_POST['OrderBy'] , $TableName);
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
    			$SetData  = $Select->SetData($TableName , $ColumnListString , $WhereString , $GroupByString , $OrderByString , $LimitNumberRows , $AggregateFunctionArray , $Params);
    			$MakeQuery  = $Select->MakeQuery($SetData);
    			$Result  = $Select->ExecuteData($MakeQuery , $SetData);
    			if($Result)
    			{
     				SUCCESS();
     				return;
    			}
    			else
    			{
     				ERROR('Cannot Select ' . $TableName . '`s data');
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