<?php
declare(strict_types=1);

namespace Meraki\Route;

use Meraki\Route\Constraint;
use Meraki\TestSuite;

final class ConstraintTest extends TestSuite
{
	/**
	 * @test
	 * @dataProvider validAlphaSegments
	 */
	public function alpha_constraint_matches_against_only_alphabetic_characters(string $segment): void
	{
		$this->assertConstraintMatched(Constraint::alpha(), $segment);
	}

	public function validAlphaSegments(): array
	{
		return [
			'lowercase (single)' => ['a'],
			'uppercase (single)' => ['A'],
			'lowercase (multiple)' => ['helloworld'],
			'uppercase (multiple)' => ['HELLOWORLD'],
			'mixed case' => ['HelloWorld']
		];
	}

	/**
	 * @test
	 * @dataProvider invalidAlphaSegments
	 */
	public function alpha_constraint_does_not_match_against_non_alphabetic_only_characters(string $segment): void
	{
		$this->assertConstraintNotMatched(Constraint::alpha(), $segment);
	}

	public function invalidAlphaSegments(): array
	{
		return [
			'empty' => [''],
			'numbers' => ['123'],
			'alpha numeric' => ['x1y2z3'],
			'hexadecimal' => ['FF00FF'],
			'alpha with dashes' => ['hello-world']
		];
	}

	/**
	 * @test
	 * @dataProvider validDigitSegments
	 */
	public function digit_constraint_matches_against_whole_numbers_only(string $segment): void
	{
		$this->assertConstraintMatched(Constraint::digit(), $segment);
	}

	public function validDigitSegments(): array
	{
		return [
			'single digit' => ['1'],
			'multiple digits' => ['465']
		];
	}

	/**
	 * @test
	 * @dataProvider invalidDigitSegments
	 */
	public function digit_constraint_does_not_match_against_non_whole_numbers(string $segment): void
	{
		$this->assertConstraintNotMatched(Constraint::digit(), $segment);
	}

	public function invalidDigitSegments(): array
	{
		return [
			'empty' => [''],
			'alphabetic' => ['HelloWorld'],
			'alpha numeric' => ['x1y2z3'],
			'hexadecimal' => ['FF00FF'],
			'decimal numbers' => ['3.14']
		];
	}

	/**
	 * @test
	 * @dataProvider validHexSegments
	 */
	public function hex_constraint_matches_against_hexadecimal_characters(string $segment): void
	{
		$this->assertConstraintMatched(Constraint::hex(), $segment);
	}

	public function validHexSegments(): array
	{
		return [
			'lowercase' => ['ececec'],
			'uppercase' => ['FFFFFF'],
			'mixed case without numbers' => ['ecECec'],
			'mixed case with numbers' => ['ff00EC']
		];
	}

	/**
	 * @test
	 * @dataProvider invalidHexSegments
	 */
	public function hex_constraint_does_not_match_against_non_hexadecimal_characters(string $segment): void
	{
		$this->assertConstraintNotMatched(Constraint::hex(), $segment);
	}

	public function invalidHexSegments(): array
	{
		return [
			'empty' => [''],
			'alphabetic' => ['HelloWorld'],
			'alpha numeric (outside hex range)' => ['x1y2z3'],
			'decimal numbers' => ['3.14']
		];
	}

	/**
	 * @test
	 * @dataProvider validAnySegments
	 */
	public function any_constraint_matches_any_character_upto_next_forward_slash(string $segment): void
	{
		$this->assertConstraintMatched(Constraint::any(), $segment);
	}

	public function validAnySegments(): array
	{
		return [
			'letters with space' => ['hello world'],
			'letters mixed case' => ['HelloWorld'],
			'hex' => ['ecECec'],
			'decimal' => ['3.14'],
			'slug' => ['hello-world'],
			'digits' => ['123'],
			'snake case' => ['hello_world']
		];
	}

	/**
	 * @test
	 */
	public function any_constraint_does_not_match_against_empty_segment(): void
	{
		$this->assertConstraintNotMatched(Constraint::any(), '');
	}

	/**
	 * @test
	 */
	public function any_constraint_does_not_match_against_segment_with_slash_in_segment(): void
	{
		$this->assertConstraintNotMatched(Constraint::any(), 'ab/cd');
	}

	/**
	 * @test
	 */
	public function custom_constraint_escapes_delimiters(): void
	{
		$constraint = Constraint::custom('[a-zA-Z~]+');

		$this->assertEquals('[a-zA-Z\~]+', $constraint->getRegex());
	}

	/**
	 * @test
	 */
	public function custom_constraint_can_be_matched(): void
	{
		$constraint = Constraint::custom('[a-zA-Z~]+');

		$this->assertConstraintMatched($constraint, 'hello~world');
	}

	/**
	 * @test
	 * @dataProvider validAlphaNumericSegments
	 */
	public function alpha_numeric_constraint_matches_letters_and_digit(string $segment): void
	{
		$this->assertConstraintMatched(Constraint::alphaNumeric(), $segment);
	}

	public function validAlphaNumericSegments(): array
	{
		return [
			'letters' => ['helloworld'],
			'digits' => ['1234'],
			'letters and digits (lowercase)' => ['h3ll0w0r1d'],
			'letters and digits (uppercase)' => ['H3LL0W0R1D'],
			'letters and digits (mixed case)' => ['h3ll0W0R1D']
		];
	}

	/**
	 * @test
	 * @dataProvider invalidAlphaNumericSegments
	 */
	public function alpha_numeric_constraint_does_not_match_against_non_letters_and_digit(string $segment): void
	{
		$this->assertConstraintNotMatched(Constraint::alphaNumeric(), $segment);
	}

	public function invalidAlphaNumericSegments(): array
	{
		return [
			'empty' => [''],
			'snake case' => ['hello_world'],
			'slug' => ['hello-world'],
			'forward slash' => ['hello/world'],
			'decimal' => ['3.14']
		];
	}

	/**
	 * @test
	 * @dataProvider validSlugSegments
	 */
	public function slug_constraint_matches_letters_digits_and_dashes(string $segment): void
	{
		$this->assertConstraintMatched(Constraint::slug(), $segment);
	}

	public function validSlugSegments(): array
	{
		return [
			'letters' => ['hello-world'],
			'digits' => ['12-34'],
			'letters and digits (mixed case)' => ['h3ll0-W0R1D']
		];
	}

	/**
	 * @test
	 * @dataProvider invalidSlugSegments
	 */
	public function slug_constraint_does_not_match_non_letters_digits_and_dashes(string $segment): void
	{
		$this->assertConstraintNotMatched(Constraint::slug(), $segment);
	}

	public function invalidSlugSegments(): array
	{
		return [
			'empty' => [''],
			'snake case' => ['hello_world'],
			'forward slash' => ['hello/world'],
			'decimal' => ['3.14'],
			'percent encoding' => ['hello%20world']
		];
	}

	protected function assertConstraintMatched(Constraint $constraint, string $segment): void
	{
		$matched = preg_match('~^' . $constraint->getRegex() . '$~', $segment);

		$this->assertTrue((bool) $matched);
	}

	protected function assertConstraintNotMatched(Constraint $constraint, string $segment): void
	{
		$matched = preg_match('~^' . $constraint->getRegex() . '$~', $segment);

		$this->assertFalse((bool) $matched);
	}
}
