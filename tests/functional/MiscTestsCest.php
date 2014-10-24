<?php
use \FunctionalTester;

class MiscTestsCest
{

    public function test_the_404_filter(FunctionalTester $I)
    {
        $I->wantTo('hit a page that shouldnt be there and make sure it fires the exception');

        \PHPUnit_Framework_Assert::assertTrue(
            $I->seeExceptionThrown('Symfony\Component\HttpKernel\Exception\NotFoundHttpException', function () use ($I) {
                $I->amOnPage('/shouldntbethere');
                $I->shouldSee('The route you requested doesn\'t exist.');
            })
        );

    }

    public function test_the_api_404_filter(FunctionalTester $I)
    {
        $I->wantTo('hit an api page that shouldnt be there and make sure it fires the exception');

        \PHPUnit_Framework_Assert::assertTrue(
            $I->seeExceptionThrown('Symfony\Component\HttpKernel\Exception\NotFoundHttpException', function () use ($I) {
                $I->amOnPage('/api/shouldntbethere');
                $I->shouldSee('"status": 404');
            })
        );

    }
}
