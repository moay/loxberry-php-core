<?php

namespace LoxBerry\Tests\Helpers;

use PHPUnit\Framework\IncompleteTestError;
use PHPUnit\Framework\SkippedTestError;

trait RetryTrait
{
    public function runBare(): void
    {
        $e = null;

        $numberOfRetries = $this->getNumberOfRetries();
        if (false == is_numeric($numberOfRetries)) {
            throw new \LogicException(sprintf('The $numberOfRetries must be a number but got "%s"', var_export($numberOfRetries, true)));
        }
        $numberOfRetries = (int) $numberOfRetries;
        if ($numberOfRetries <= 0) {
            throw new \LogicException(sprintf('The $numberOfRetries must be a positive number greater than 0 but got "%s".', $numberOfRetries));
        }

        for ($i = 0; $i < $numberOfRetries; ++$i) {
            try {
                parent::runBare();

                return;
            } catch (IncompleteTestError $e) {
                throw $e;
            } catch (SkippedTestError $e) {
                throw $e;
            } catch (\Throwable $e) {
                // last one thrown below
            } catch (\Exception $e) {
                // last one thrown below
            }
        }

        if ($e) {
            throw $e;
        }
    }

    /**
     * @return int
     */
    private function getNumberOfRetries()
    {
        $annotations = $this->getAnnotations();

        if (isset($annotations['method']['retry'][0])) {
            return $annotations['method']['retry'][0];
        }

        if (isset($annotations['class']['retry'][0])) {
            return $annotations['class']['retry'][0];
        }

        return 1;
    }
}
