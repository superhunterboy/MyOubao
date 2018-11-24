<?php
class DigitalNumber
{
	static public function getShape($sNumber)
	{
		$iDitigtal = strlen($sNumber);
		$aDitigtals = str_split($sNumber);
		sort($aDitigtals);
		$aValues = array_count_values($aDitigtals);

		switch (count($aValues)) {
		case 1:
			return 0;
			break;

		case 2:
			switch ($iDitigtal) {
			case 2:
				return 2;
				break;

			case 3:
				return 3;
				break;

			case 4:
				$iMaxCount = max($aValues);
				return $iMaxCount == 3 ? 4 : 6;
				break;

			case 5:
				$iMaxCount = max($aValues);
				return $iMaxCount == 4 ? 5 : 10;
				break;
			}

			break;

		case 3:
			switch ($iDitigtal) {
			case 3:
				return 6;
				break;

			case 4:
				return 12;
				break;

			case 5:
				$iMaxCount = max($aValues);
				return $iMaxCount == 3 ? 20 : 30;
				break;
			}

			break;

		case 4:
			switch ($iDitigtal) {
			case 4:
				return 24;
				break;

			case 5:
				return 60;
			}

			break;

		case 5:
			return 120;
		}
	}

	static public function checkCode(&$sCode, $iCodeLen, $sShape = -1)
	{
		$sPattern = ($sShape == 16 ? '/^[0123]{1,4}\\,[0123]{1,4}$/' : '/^\\d{' . $iCodeLen . '}$/');

		if (!preg_match($sPattern, $sCode)) {
			return false;
		}

		if ((-1 < $sShape) && !in_array($sShape, array(1, 16))) {
			$aShape = explode(',', $sShape);

			if (!in_array(self::getShape($sCode), $aShape)) {
				return false;
			}

			$a = str_split($sCode, 1);
			sort($a);
			$sCode = implode($a);
		}

		return true;
	}

	static public function getCombinNumber($sNumber, $bUnique = false)
	{
		if (!self::getShape($sNumber)) {
			return '';
		}

		$aWei = str_split($sNumber, 1);
		!$bUnique || ($aWei = array_unique($aWei));
		sort($aWei);
		return implode($aWei);
	}

	static public function getSum($sNumber)
	{
		$aWei = str_split($sNumber);
		return array_sum($aWei);
	}

	static public function getSumTail($sNumber)
	{
		return self::getSum($sNumber) % 10;
	}

	static public function getSpan($sNumber)
	{
		$aWei = str_split($sNumber);
		return max($aWei) - min($aWei);
	}
}


?>