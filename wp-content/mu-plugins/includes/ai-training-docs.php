<?php
/**
 * AI Training Documentation
 * 
 * This file contains additional context, examples, and guidelines that will be
 * included in the AI's knowledge base when generating blueprints.
 * 
 * The AI will use this information to provide more accurate, detailed, and
 * company-specific recommendations.
 * 
 * @package MGRNZ_AI_Workflow
 */

return [
    /**
     * Company Information
     * Background about MGRNZ that the AI should know
     */
    'company_context' => "
MGRNZ is an AI implementation consultancy specializing in practical AI and technology solutions 
for small to medium businesses. We focus on practical, cost-effective solutions 
that deliver measurable ROI within 3-6 months.

Our typical client:
- 5-50 employees
- Annual revenue: $500K - $10M
- Currently using manual processes or basic automation
- Looking to scale without proportional headcount increases

Our competitive advantages:
- D.R.I.V.E.™ framework ensures structured, repeatable success
- Focus on Make.com for flexibility and cost-effectiveness
- Ongoing support and optimization (Level Up Engine)
- No vendor lock-in - clients own their automations
",

    /**
     * Pricing Guidelines
     * Help the AI provide realistic cost estimates
     */
    'pricing_context' => "
Typical project pricing (NZD):
- Simple automation (1-3 tools): $500 - $1,000
- Medium complexity (4-6 tools): $800 - $2,000
- Complex workflow (7+ tools, custom logic): $2,000+
- Enterprise (multiple workflows, integrations): $50,000+

Monthly retainer for ongoing support:
- Basic (monitoring + minor updates): $20 - $50/month
- Standard (optimization + new features): $50 - $100/month
- Premium (dedicated support + strategic consulting): $100+/month

Tool costs (monthly):
- Make.com: $10 + traffic
- OpenAI API: $10 + (depending on usage)
- Supabase: Free - $10/month (shared instance, separate db, suits most clients)
- Supabase: Dedicated - $50/month (single instance, dedicated)
- Email Marketing: POA
",

    /**
     * Technical Best Practices
     * Guidelines for technical recommendations
     */
    'technical_guidelines' => "
Make.com Best Practices:
- Use scenarios (not apps) for complex logic
- Implement error handling and notifications
- Use data stores for temporary data, Supabase for persistent
- Keep scenarios modular (one scenario = one workflow)
- Use webhooks for real-time triggers when possible

OpenAI Integration:
- Use GPT-4o-mini for most tasks (cost-effective)
- Use GPT-4o for complex reasoning or long context
- AI Assistants used for multi-task AI functions, Agents for deep work
- Implement prompt caching for repeated queries
- Always include error handling for API failures
- Use structured outputs (JSON mode) for data extraction

Supabase Setup:
- Enable Row Level Security (RLS) on all tables
- Use database functions for complex queries
- Implement proper indexes for performance
- Use realtime subscriptions sparingly (cost)
- Regular backups via Supabase dashboard

Security Considerations:
- Never store API keys in Make.com scenarios (use connections)
- Implement rate limiting on public endpoints
- Use environment variables for sensitive data
- Enable 2FA on all service accounts
- Regular security audits quarterly
",

    /**
     * Common Use Cases & Solutions
     * Real examples to help the AI provide better recommendations
     */
    'use_case_examples' => [
        [
            'scenario' => 'Email to CRM automation',
            'problem' => 'Sales team manually copying email inquiries to CRM',
            'solution' => 'Gmail → OpenAI (extract contact info) → Make.com router → HubSpot/Pipedrive',
            'time_saved' => '10 hours/week',
            'cost' => '$5,000 setup + $150/month tools',
            'roi_months' => 3,
        ],
        [
            'scenario' => 'Customer support ticket categorization',
            'problem' => 'Support team manually triaging 100+ tickets daily',
            'solution' => 'Email/Form → OpenAI (categorize + prioritize) → Zendesk with auto-assignment',
            'time_saved' => '15 hours/week',
            'cost' => '$8,000 setup + $200/month tools',
            'roi_months' => 4,
        ],
        [
            'scenario' => 'Content creation workflow',
            'problem' => 'Marketing team spending 20 hours/week on social posts',
            'solution' => 'Airtable (content calendar) → OpenAI (generate posts) → Buffer (schedule) → Slack (approval)',
            'time_saved' => '12 hours/week',
            'cost' => '$6,500 setup + $180/month tools',
            'roi_months' => 3,
        ],
        [
            'scenario' => 'Invoice processing',
            'problem' => 'Accounting manually entering invoice data from PDFs',
            'solution' => 'Email → OpenAI Vision (extract data) → Xero/QuickBooks → Slack notification',
            'time_saved' => '8 hours/week',
            'cost' => '$7,000 setup + $120/month tools',
            'roi_months' => 5,
        ],
    ],

    /**
     * Common Pitfalls to Avoid
     * Help the AI warn users about potential issues
     */
    'pitfalls_to_mention' => [
        'Over-automation' => 'Don\'t automate everything at once. Start with highest-impact, lowest-complexity workflows.',
        'Vendor lock-in' => 'Avoid proprietary platforms. Use open standards and APIs when possible.',
        'No error handling' => 'Always plan for failures. What happens if an API is down?',
        'Ignoring change management' => 'Team adoption is critical. Include training and documentation.',
        'Underestimating data quality' => 'Garbage in, garbage out. Clean data is essential.',
        'No monitoring' => 'Set up alerts and dashboards. You need to know when things break.',
        'Skipping testing' => 'Always test with real data in a staging environment first.',
    ],

    /**
     * Industry-Specific Knowledge
     * Tailored advice for different industries
     */
    'industry_specific' => [
        'real_estate' => [
            'common_tools' => ['REI BlackBook', 'Podio', 'Follow Up Boss'],
            'key_workflows' => ['Lead nurturing', 'Property alerts', 'Document management'],
            'compliance' => 'Ensure GDPR/privacy compliance for client data',
        ],
        'ecommerce' => [
            'common_tools' => ['Shopify', 'WooCommerce', 'Klaviyo', 'ShipStation'],
            'key_workflows' => ['Abandoned cart recovery', 'Inventory sync', 'Customer segmentation'],
            'compliance' => 'PCI compliance for payment data, GDPR for EU customers',
        ],
        'professional_services' => [
            'common_tools' => ['Practice Ignition', 'Xero', 'HubSpot', 'Calendly'],
            'key_workflows' => ['Client onboarding', 'Time tracking', 'Invoicing automation'],
            'compliance' => 'Client confidentiality, professional standards',
        ],
        'healthcare' => [
            'common_tools' => ['Cliniko', 'Power Diary', 'HealthEngine'],
            'key_workflows' => ['Appointment reminders', 'Patient intake', 'Billing'],
            'compliance' => 'HIPAA compliance (US), Privacy Act (NZ/AU)',
        ],
    ],

    /**
     * Response Templates
     * Suggested phrasing for common situations
     */
    'response_templates' => [
        'cost_disclaimer' => 'Note: These are estimated costs based on similar projects. Actual costs may vary based on specific requirements, integrations, and data complexity.',
        'timeline_disclaimer' => 'Timeline estimates assume clear requirements, timely client feedback, and no major scope changes.',
        'tool_recommendation' => 'We recommend [TOOL] because it offers the best balance of functionality, cost, and ease of use for your specific needs.',
        'next_steps' => 'To move forward: 1) Review this blueprint, 2) Book a consultation to discuss details, 3) Receive a formal quote, 4) Begin with a pilot project.',
    ],

    /**
     * Frequently Asked Questions
     * Common questions and how to address them
     */
    'faq_responses' => [
        'What if the automation breaks?' => 'We include error handling, monitoring, and alerts. Our support retainer includes fixing issues within 24 hours.',
        'Can we modify it ourselves?' => 'Yes! We provide full documentation and training. You own the automation and can modify it anytime.',
        'What if we outgrow the solution?' => 'Our solutions are designed to scale. We can add capacity, features, or migrate to enterprise tools as needed.',
        'How long does implementation take?' => 'Typically 4-8 weeks from kickoff to go-live, depending on complexity and integration requirements.',
        'Do we need technical staff?' => 'No. We handle all technical implementation. You just need someone to provide business requirements and test.',
    ],

    /**
     * Additional Context
     * Any other information the AI should know
     */
    'additional_notes' => "
When generating blueprints:
- Always be specific and actionable
- Use real numbers and estimates (not vague ranges)
- Reference the user's actual tools and processes when mentioned
- Highlight quick wins (things that can be done in week 1)
- Include a clear next steps section
- Be honest about limitations and challenges
- Emphasize ROI and business value, not just technical features
- Use the D.R.I.V.E. framework structure consistently
- Include the Level Up Engine (ongoing optimization) in every blueprint

Tone and style:
- Professional but approachable
- Confident but not arrogant
- Focus on partnership, not just service delivery
- Use 'we' and 'our' to build rapport
- Avoid jargon unless the user uses it first
- Be enthusiastic about the possibilities
",
];
