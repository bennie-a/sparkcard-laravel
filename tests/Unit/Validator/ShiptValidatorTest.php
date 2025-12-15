<?php

namespace Tests\Unit\Validator;

use App\Http\Response\CustomResponse;
use App\Http\Validator\ShiptValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;
use Tests\Unit\DB\Shipt\ShiptLogTestHelper;
use App\Services\Constant\ShiptConstant as SC;
use Mockery;
use Tests\Trait\ApiErrorAssertions;
use Tests\Trait\PostApiAssertions;

/**
 * ShiptValidatorのテストクラス
 */
#[CoversClass(ShiptValidator::class)]
class ShiptValidatorTest extends TestCase
{
}
