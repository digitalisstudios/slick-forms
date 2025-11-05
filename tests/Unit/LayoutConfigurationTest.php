<?php

namespace DigitalisStudios\SlickForms\Tests\Unit;

use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;
use DigitalisStudios\SlickForms\Tests\TestCase;

/**
 * Test advanced layout element configuration (Rows, Columns, Cards, Tabs, Accordions)
 */
class LayoutConfigurationTest extends TestCase
{
    /** @test */
    public function row_helper_methods_generate_correct_classes()
    {
        $row = SlickFormLayoutElement::create([
            'slick_form_id' => CustomForm::factory()->create()->id,
            'element_type' => 'row',
            'settings' => [
                'gutter' => 'g-3',
                'horizontal_alignment' => 'justify-content-center',
                'vertical_alignment' => 'align-items-start',
            ],
        ]);

        $classes = $row->getRowClass();

        $this->assertStringContainsString('row', $classes);
        $this->assertStringContainsString('g-3', $classes);
        $this->assertStringContainsString('justify-content-center', $classes);
        $this->assertStringContainsString('align-items-start', $classes);
    }

    /** @test */
    public function column_supports_xxl_breakpoint()
    {
        $column = SlickFormLayoutElement::create([
            'slick_form_id' => CustomForm::factory()->create()->id,
            'element_type' => 'column',
            'settings' => [
                'column_width' => [
                    'xs' => '12',
                    'md' => '6',
                    'xxl' => '4',
                ],
            ],
        ]);

        $classes = $column->getColumnClass();

        $this->assertStringContainsString('col-12', $classes);
        $this->assertStringContainsString('col-md-6', $classes);
        $this->assertStringContainsString('col-xxl-4', $classes);
    }

    /** @test */
    public function column_offset_generates_correct_classes()
    {
        $column = SlickFormLayoutElement::create([
            'slick_form_id' => CustomForm::factory()->create()->id,
            'element_type' => 'column',
            'settings' => [
                'column_offset' => [
                    'md' => '2',
                    'lg' => '3',
                ],
            ],
        ]);

        $classes = $column->getColumnClass();

        $this->assertStringContainsString('offset-md-2', $classes);
        $this->assertStringContainsString('offset-lg-3', $classes);
    }

    /** @test */
    public function column_order_generates_correct_classes()
    {
        $column = SlickFormLayoutElement::create([
            'slick_form_id' => CustomForm::factory()->create()->id,
            'element_type' => 'column',
            'settings' => [
                'column_order' => [
                    'md' => 'first',
                    'lg' => '2',
                ],
            ],
        ]);

        $classes = $column->getColumnClass();

        $this->assertStringContainsString('order-md-first', $classes);
        $this->assertStringContainsString('order-lg-2', $classes);
    }

    /** @test */
    public function column_self_alignment_generates_correct_class()
    {
        $column = SlickFormLayoutElement::create([
            'slick_form_id' => CustomForm::factory()->create()->id,
            'element_type' => 'column',
            'settings' => [
                'align_self' => 'align-self-center',
            ],
        ]);

        $classes = $column->getColumnClass();

        $this->assertStringContainsString('align-self-center', $classes);
    }

    /** @test */
    public function container_breakpoint_generates_correct_class()
    {
        $container = SlickFormLayoutElement::create([
            'slick_form_id' => CustomForm::factory()->create()->id,
            'element_type' => 'container',
            'settings' => [
                'container_breakpoint' => 'lg',
            ],
        ]);

        $class = $container->getContainerClass();

        $this->assertEquals('container-lg', $class);
    }

    /** @test */
    public function container_fluid_generates_correct_class()
    {
        $container = SlickFormLayoutElement::create([
            'slick_form_id' => CustomForm::factory()->create()->id,
            'element_type' => 'container',
            'settings' => [
                'container_fluid' => true,
            ],
        ]);

        $class = $container->getContainerClass();

        $this->assertEquals('container-fluid', $class);
    }

    /** @test */
    public function card_helper_methods_work_correctly()
    {
        $card = SlickFormLayoutElement::create([
            'slick_form_id' => CustomForm::factory()->create()->id,
            'element_type' => 'card',
            'settings' => [
                'card_header_show' => true,
                'card_header_text' => 'Personal Information',
                'card_footer_show' => true,
                'card_footer_text' => 'Required fields marked with *',
                'card_title' => 'Contact Details',
                'card_subtitle' => 'Please provide accurate information',
                'card_background' => 'light',
                'card_border_color' => 'primary',
                'card_shadow' => 'shadow',
            ],
        ]);

        $this->assertTrue($card->hasCardHeader());
        $this->assertEquals('Personal Information', $card->getCardHeaderText());
        $this->assertTrue($card->hasCardFooter());
        $this->assertEquals('Required fields marked with *', $card->getCardFooterText());
        $this->assertEquals('Contact Details', $card->getCardTitle());
        $this->assertEquals('Please provide accurate information', $card->getCardSubtitle());

        $classes = $card->getCardClass();
        $this->assertStringContainsString('card', $classes);
        $this->assertStringContainsString('bg-light', $classes);
        $this->assertStringContainsString('border-primary', $classes);
        $this->assertStringContainsString('shadow', $classes);
    }

    /** @test */
    public function tabs_helper_methods_work_correctly()
    {
        $tabs = SlickFormLayoutElement::create([
            'slick_form_id' => CustomForm::factory()->create()->id,
            'element_type' => 'tabs',
            'settings' => [
                'tab_style' => 'nav-pills',
                'tab_alignment' => 'justify-content-center',
                'tab_fill' => 'nav-fill',
                'tab_fade' => true,
                'default_active_tab' => 1,
            ],
        ]);

        $classes = $tabs->getTabsClass();
        $this->assertStringContainsString('nav', $classes);
        $this->assertStringContainsString('nav-pills', $classes);
        $this->assertStringContainsString('justify-content-center', $classes);
        $this->assertStringContainsString('nav-fill', $classes);

        $this->assertTrue($tabs->hasFadeAnimation());
        $this->assertEquals(1, $tabs->getDefaultActiveTab());
    }

    /** @test */
    public function accordion_helper_methods_work_correctly()
    {
        $accordion = SlickFormLayoutElement::create([
            'slick_form_id' => CustomForm::factory()->create()->id,
            'element_type' => 'accordion',
            'settings' => [
                'accordion_flush' => true,
                'always_open' => true,
                'default_open_item' => 0,
            ],
        ]);

        $classes = $accordion->getAccordionClass();
        $this->assertStringContainsString('accordion', $classes);
        $this->assertStringContainsString('accordion-flush', $classes);

        $this->assertTrue($accordion->isAccordionAlwaysOpen());
        $this->assertEquals(0, $accordion->getDefaultOpenItem());
    }
}
