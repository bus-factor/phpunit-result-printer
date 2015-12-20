<?php

namespace PhpUnitResultPrinter;

use PHPUnit_TextUI_ResultPrinter;

class ResultPrinter extends PHPUnit_TextUI_ResultPrinter
{
    /**
     * @param PHPUnit_Framework_TestResult $result
     */
    public function printResult(PHPUnit_Framework_TestResult $result)
    {
        parent::printResult($result);

        if ($result->getCollectCodeCoverageInformation()) {
            $this->printCodeCoverage($result->getCodeCoverage());
        }
    }

    protected function printCodeCoverage($codeCoverage)
    {
        $report = $codeCoverage->getReport();
        $coverage = PHP_CodeCoverage_Util::percent($report->getNumExecutedLines(), $report->getNumExecutableLines());
        $text = sprintf('Coverage: %.2f%% (%d/%d)', $coverage, $report->getNumExecutedLines(), $report->getNumExecutableLines());

        if ($coverage >= 100) {
            $this->writeWithColor('fg-white', $text);
        } else {
            $this->writeWithColor('fg-white, bold, bg-red', $text);
        }
    }

    protected function printHeader()
    {
        $header = sprintf(
            "\n\nFinished in %.3f seconds. Peak memory usage was %.2fMb.\n",
            PHP_Timer::timeSinceStartOfRequest(),
            memory_get_peak_usage(true) / 1048576
        );

        $this->write($header);
    }

    /**
     * @param PHPUnit_Framework_TestResult $result
     */
    protected function printFooter(PHPUnit_Framework_TestResult $result)
    {
        $tests = count($result);
        $testsText = sprintf('%d test%s', $tests, ($tests == 1) ? '' : 's');

        if (count($result) === 0) {
            $this->writeWithColor('fg-green', sprintf('%s, 0 failures.', $testsText));
        } else {
            $wasOk = $result->wasSuccessful()
                && $result->allHarmless()
                && $result->allCompletelyImplemented()
                && $result->noneSkipped();

            if ($wasOk) {
                $this->writeWithColor('fg-green', sprintf('%s, 0 failures.', $testsText));
            } else {
                if ($this->verbose) {
                    $this->write("\n");
                }

                if ($result->wasSuccessful()) {
                    $skipped = $result->skippedCount();
                    $skippedText = ($skipped > 0) ? sprintf(', %d skipped test%s', $skipped, ($skipped > 0) ? 's' : '') : '';

                    $incomplete = $result->notImplementedCount();
                    $incompleteText = ($incomplete > 0) ? sprintf(', %d incomplete test%s', $incomplete, ($incomplete > 0) ? 's' : '') : '';

                    $risky = $result->riskyCount();
                    $riskyText = ($risky > 0) ? sprintf(', %d risky test%s', $risky, ($risky > 0) ? 's' : '') : '';

                    $this->writeWithColor('fg-yellow', sprintf('%s%s%s, 0 failures.', $testsText, $skippedText, $incompleteText, $riskyText)
                    );
                } else {
                    $errors = $result->errorCount();
                    $errorsText = ($errors > 0) ? sprintf(', %d error%s', $errors, ($errors > 0) ? 's' : '') : '';

                    $failures = $result->failureCount();
                    $failuresText = ($failures > 0) ? sprintf(', %d failure%s', $failures, ($failures > 0) ? 's' : '') : '';

                    $this->writeWithColor('fg-red', sprintf('%s%s%s.', $testsText, $errorsText, $failuresText));
                }
            }
        }
    }

    /**
     * A testsuite started.
     *
     * @param PHPUnit_Framework_TestSuite $suite
     *
     * @since  Method available since Release 2.2.0
     */
    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        if ($this->numTests == -1) {
            $this->numTests = count($suite);
        }
    }

    /**
     * A test ended.
     *
     * @param PHPUnit_Framework_Test $test
     * @param float                  $time
     */
    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        if (!$this->lastTestFailed) {
            $this->writeProgressWithColor('fg-green', '.');
        }

        if ($test instanceof PHPUnit_Framework_TestCase) {
            $this->numAssertions += $test->getNumAssertions();
        } elseif ($test instanceof PHPUnit_Extensions_PhptTestCase) {
            $this->numAssertions++;
        }

        $this->lastTestFailed = false;

        if ($test instanceof PHPUnit_Framework_TestCase) {
            if (!$test->hasExpectationOnOutput()) {
                $this->write($test->getActualOutput());
            }
        }
    }

    protected function writeProgress($progress)
    {
        $this->write($progress);
    }
}
