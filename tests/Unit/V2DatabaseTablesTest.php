<?php

namespace DigitalisStudios\SlickForms\Tests\Unit;

use DigitalisStudios\SlickForms\Tests\TestCase;
use Illuminate\Support\Facades\Schema;

/**
 * Test V2 database tables exist and have correct structure
 */
class V2DatabaseTablesTest extends TestCase
{
    /** @test */
    public function slick_form_email_templates_table_exists()
    {
        $this->assertTrue(Schema::hasTable('slick_form_email_templates'));

        $this->assertTrue(Schema::hasColumns('slick_form_email_templates', [
            'id', 'form_id', 'type', 'enabled', 'recipients', 'subject',
            'body_template', 'attach_pdf', 'conditional_rules', 'priority',
            'created_at', 'updated_at',
        ]));
    }

    /** @test */
    public function slick_form_email_logs_table_exists()
    {
        $this->assertTrue(Schema::hasTable('slick_form_email_logs'));

        $this->assertTrue(Schema::hasColumns('slick_form_email_logs', [
            'id', 'submission_id', 'template_id', 'to', 'subject', 'body',
            'status', 'error_message', 'sent_at', 'created_at', 'updated_at',
        ]));
    }

    /** @test */
    public function slick_form_spam_logs_table_exists()
    {
        $this->assertTrue(Schema::hasTable('slick_form_spam_logs'));

        $this->assertTrue(Schema::hasColumns('slick_form_spam_logs', [
            'id', 'form_id', 'ip_address', 'detection_method', 'details',
            'created_at', 'updated_at',
        ]));
    }

    /** @test */
    public function slick_dynamic_options_cache_table_exists()
    {
        $this->assertTrue(Schema::hasTable('slick_dynamic_options_cache'));

        $this->assertTrue(Schema::hasColumns('slick_dynamic_options_cache', [
            'id', 'field_id', 'cache_key', 'options', 'cached_at', 'ttl_seconds',
            'created_at', 'updated_at',
        ]));
    }

    /** @test */
    public function slick_form_model_bindings_table_exists()
    {
        $this->assertTrue(Schema::hasTable('slick_form_model_bindings'));

        $this->assertTrue(Schema::hasColumns('slick_form_model_bindings', [
            'id', 'form_id', 'model_class', 'field_mappings', 'allow_create',
            'allow_update', 'route_parameter', 'created_at', 'updated_at',
        ]));
    }

    /** @test */
    public function slick_form_signed_urls_table_exists()
    {
        $this->assertTrue(Schema::hasTable('slick_form_signed_urls'));

        $this->assertTrue(Schema::hasColumns('slick_form_signed_urls', [
            'id', 'form_id', 'signature', 'prefill_data', 'expires_at',
            'max_uses', 'uses', 'created_at', 'updated_at',
        ]));
    }

    /** @test */
    public function slick_form_webhooks_table_exists()
    {
        $this->assertTrue(Schema::hasTable('slick_form_webhooks'));

        $this->assertTrue(Schema::hasColumns('slick_form_webhooks', [
            'id', 'form_id', 'name', 'url', 'method', 'headers', 'enabled',
            'trigger_conditions', 'created_at', 'updated_at',
        ]));
    }

    /** @test */
    public function slick_form_webhook_logs_table_exists()
    {
        $this->assertTrue(Schema::hasTable('slick_form_webhook_logs'));

        $this->assertTrue(Schema::hasColumns('slick_form_webhook_logs', [
            'id', 'webhook_id', 'submission_id', 'event_type', 'request_url',
            'request_headers', 'request_body', 'response_status', 'response_headers',
            'response_body', 'status', 'retry_count', 'error_message',
            'sent_at', 'created_at', 'updated_at',
        ]));
    }

    /** @test */
    public function slick_form_versions_table_exists()
    {
        $this->assertTrue(Schema::hasTable('slick_form_versions'));

        $this->assertTrue(Schema::hasColumns('slick_form_versions', [
            'id', 'form_id', 'version_number', 'form_snapshot', 'change_summary',
            'published_by', 'published_at', 'created_at', 'updated_at',
        ]));
    }

    /** @test */
    public function slick_forms_table_has_v2_columns()
    {
        $this->assertTrue(Schema::hasColumns('slick_forms', [
            'uuid', 'url_strategy', 'hashid_salt',
        ]));
    }

    /** @test */
    public function slick_form_submissions_table_has_v2_columns()
    {
        $this->assertTrue(Schema::hasColumns('slick_form_submissions', [
            'form_version_id', 'model_type', 'model_id',
        ]));
    }
}
