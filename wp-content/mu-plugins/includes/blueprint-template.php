<?php
/**
 * Blueprint Template Configuration
 * 
 * Customize the AI-generated blueprint structure and content here.
 * This file is loaded by class-ai-service.php
 * 
 * @package MGRNZ_AI_Workflow
 */

return [
    /**
     * System Role
     * Defines the AI's persona and expertise
     */
    'system_role' => "You are a Senior AI Workflow Architect at MGRNZ. Your goal is to faciliate the exchange of information from users interacting with a wizard to generate a workflow blueprint. Once obtained, you are using this information to design a professional, automated solution for the user.  Your design must consider the MGRNZ preferred stack, at the same time respecting that users will be working on various platforms, with various email and App providers and different preferences.  Implementation approach is to be aligned to the 'DRIVE Automation Framework', our implementation framework boorrowed from Maximised AI.",
    
    /**
     * Technology Stack
     * Default tools to recommend (AI will adapt based on user needs)
     */
    'tech_stack' => [
        'AI Engine' => 'OpenAI / ChatGPT or Google / Gemini',
        'Coding-Agent' => 'Kiro.dev (AWS), Antigravity (Google), Google Jules, Codexc, ChatGPT',
        'Orchestration' => 'Make.com (for all automation flows)',
        'Database' => 'Supabase (for structured data storage)',
        'Email/Marketing' => 'MailerLite (this is the product that MGRNZ use however, when relevant, its best to ask the user which package they use.  If they dont have one, please note this in the blueprint so that quote preparation includes a vendor selection process.',
        'Productivity' => 'Google Workspace or Outlook (align with user\'s current tools)',
        'Version Control' => 'GitHub',
        'PC Platform' => 'Windows',
        'Website' => 'Wordpress',
        'Proxy Servers' => 'On a case by case, Cloudflare defaults if nothing else specific',
        'Chatbot' => 'For plug n play, Botpress free chatbot',
        'Bespoke UI' => 'Usually built for purpose using React / Typescript',
    ],
    
    /**
     * Blueprint Structure
     * Define the sections and their content guidelines
     */
    'blueprint_structure' => [
        [
            'number' => '1',
            'title' => 'EXECUTIVE SUMMARY',
            'guidelines' => [
                'Start each blueprint with this statement: "This blueprint is an intended to create workflows based on your stated problem and desired end state.  This document is created using AI and its purpose is to table an idea and if desired, provide an indicative cost estimate. In most cases, a thorough discovery process is necessary to tie down a workflow design. It provides a structured approach to implementation using the D.R.I.V.E.™ Automation Framework, ensuring adequate security and controls are built into the process."',
                'Brief overview of the DRIVE Framework',
                'Brief overview of the transformation.',
                'Key benefits and expected outcomes.',
            ]
        ],
        [
            'number' => '2',
            'title' => 'ABOUT YOUR 2-MINUTE AUTOMATION',
            'guidelines' => [
                'Provide a grounded, contextual overview of the user\'s specific workflow type.',
                'Explain what this type of automation is and why it matters in their industry/context.',
                'Describe the typical components and technologies involved in this kind of workflow.',
                'Highlight common watch-outs, challenges, or considerations specific to this automation type.',
                'Make the user feel their idea is understood and validated with professional insight.',
                'Use 2-3 paragraphs to give depth and context without being overly technical.',
            ]
        ],
        [
            'number' => '3',
            'title' => 'YOUR WORKFLOW BLUEPRINT',
            'guidelines' => [
                'This section provides the detailed technical design of your automation workflow.',
                '',
                '**WORKFLOW DIAGRAM**: Create a simple, linear visual diagram showing 4-6 high-level workflow steps.',
                'IMPORTANT: Generate actual HTML with inline styles (do NOT just show the example code). Replace "Step 1 Name", "Step 2 Name" etc. with the ACTUAL step names from this workflow.',
                'Example structure (replace with actual steps):',
                '<div style="display: flex; align-items: center; gap: 10px; padding: 20px; background: #f8fafc; border-radius: 8px; margin: 20px 0; flex-wrap: wrap;">',
                '  <div style="background: #3b82f6; color: white; padding: 15px 20px; border-radius: 6px; font-weight: 600; text-align: center; min-width: 120px;">Trigger: Form Submitted</div>',
                '  <div style="font-size: 24px; color: #64748b;">→</div>',
                '  <div style="background: #3b82f6; color: white; padding: 15px 20px; border-radius: 6px; font-weight: 600; text-align: center; min-width: 120px;">Process Data</div>',
                '  <div style="font-size: 24px; color: #64748b;">→</div>',
                '  <div style="background: #3b82f6; color: white; padding: 15px 20px; border-radius: 6px; font-weight: 600; text-align: center; min-width: 120px;">Send to CRM</div>',
                '  <div style="font-size: 24px; color: #64748b;">→</div>',
                '  <div style="background: #10b981; color: white; padding: 15px 20px; border-radius: 6px; font-weight: 600; text-align: center; min-width: 120px;">Send Confirmation</div>',
                '</div>',
                'Use blue background (#3b82f6) for process steps and green (#10b981) for the final completion step. All text should be white and bold.',
                '',
                '**OUR TECH STACK**: List the specific technologies from our preferred stack that will be used. For each, explain:',
                '  • What it is (e.g., "Make.com - Visual automation platform")',
                '  • Why we\'re using it for this workflow (e.g., "Chosen for its ability to orchestrate complex multi-step workflows with 1000+ app integrations")',
                '  • Its role in this specific automation (e.g., "Will handle the trigger from form submission and coordinate all subsequent actions")',
                '',
                '**ADDITIONAL STACK**: Identify any tools or platforms OUTSIDE our standard stack. For each, explain:',
                '  • What it is and what it does',
                '  • Why it\'s necessary for this specific workflow (e.g., "Your current CRM is Salesforce, so we\'ll integrate with it rather than suggesting a replacement")',
                '  • Whether this is existing (user already has it) or new (needs to be procured)',
                '',
                '**THE WORKFLOW - STEP BY STEP**: Walk through the workflow in simple, user-friendly language. For each step in your diagram above:',
                '  • Explain what happens at this step',
                '  • Which system/tool is performing the action',
                '  • What data is being processed or transferred',
                '  • What triggers the next step',
                'Keep explanations high-level but specific to this workflow. Avoid technical jargon.',
                '',
                '**SECURITY & AUTHENTICATION**: Include a dedicated note about security. Address:',
                '  • How user data will be protected',
                '  • Authentication methods between systems (e.g., OAuth 2.0, API keys)',
                '  • Data encryption (in transit and at rest)',
                '  • Access controls and permissions',
                '  • Compliance considerations if relevant (GDPR, HIPAA, etc.)',
                '  • Backup and disaster recovery approach',
                'Frame this in terms of "Your data security" to make it personal and reassuring.',
            ]
        ],
        [
            'number' => '4',
            'title' => 'THE IMPLEMENTATION PROCESS EXPLAINED',
            'guidelines' => [
                'Include this exact introductory text:',
                '"What is a workflow? A workflow has two primary characteristics:',
                '1. A workflow involves multiple process steps, data sources, integrations of disparate tasks',
                '2. A workflow is sequential, there is a start and an end and the objective is to pass from initiation (start) to completion (end)',
                '',
                'Therefore, to implement a workflow, there are a lot of complexities to consider. It has to have an environment created to contain its code. It has multiple inputs and outputs, from often disparate systems or data sources. There are usually a number of methods to achieve each step of the workflow making its design something worth dwelling over.',
                '',
                'A workflow has to be treated with respect. While it\'s not the same as implementing new software, it should be treated in the same manner when it comes to the implementation process. It should pass through a gated framework (SDLC) of some format to ensure it will integrate safely into your business.',
                '',
                'We developed the DRIVE Automation framework for this purpose. A specific framework for workflows that\'s been trimmed to be more agile."',
                '',
                'After this introduction, provide a brief (2-3 sentence) overview of what the DRIVE framework is and why it ensures successful workflow implementation.',
            ]
        ],
        [
            'number' => '5',
            'title' => 'DISCOVER (Analysis)',
            'guidelines' => [
                'Mandatory - include this statement first "This phase focuses on identifying the need and opportunity, securing initial funding, and gaining approval for the strategic concept before proceeding."',
                'IMPORTANT: Include the DRIVE Framework diagram using this exact HTML: <img src="https://mgrnz.com/wp/wp-content/uploads/2025/11/DRIVE_Public_14-07-2025.png" alt="DRIVE Framework" style="max-width: 100%; height: auto; margin: 20px 0;" />',
                'Identify the core need and opportunity.',
                'Analyze the current state vs. future state.',
                'Highlight pain points and inefficiencies.',
            ]
        ],
        [
            'number' => '6',
            'title' => 'READY (Readiness)',
            'guidelines' => [
                'Mandatory - include this statement first "The goal is to prepare for execution by completing detailed design and brand assets, conducting a unit test, and ensuring all resources and dependencies are in place."',
                'Detailed design requirements.',
                'Data preparation and dependencies.',
                'Resource allocation and team setup.',
            ]
        ],
        [
            'number' => '7',
            'title' => 'IMPLEMENT (Execution)',
            'guidelines' => [
                'Mandatory - include this statement first "This phase involves the core main build, executing integration testing, migrating data, and conducting model training to transform the concepts into a tangible solution."',
                'The Core Build: Explain how Make.com, OpenAI, and Supabase work together.',
                'Integration steps (GitHub for code, WordPress for interface).',
                'Step-by-step implementation timeline.',
            ]
        ],
        [
            'number' => '8',
            'title' => 'VALIDATE (Quality Assurance)',
            'guidelines' => [
                'Mandatory - include this statement first "This is the monitoring and refinement stage, focused on achieving user acceptance (UAT), completing documentation, and managing support and change to optimize performance."',
                'User Acceptance Testing (UAT) criteria.',
                'Documentation and training needs.',
                'Success metrics and KPIs.',
            ]
        ],
        [
            'number' => '9',
            'title' => 'EVOLVE (Optimization)',
            'guidelines' => [
                'Mandatory to include this statementz: "The Evolve component is the dynamic, central core of the D.R.I.V.E.™ Consulting Framework, representing Elevate & Evolve. It\'s more than just the final step; it functions as the Level Up™ Engine , providing the continuous strategic oversight and quality assurance that underpins the entire methodology."',
                'The \'Level Up Engine\': How to monitor and improve over time.',
                'Future scaling opportunities.',
                'Continuous improvement strategy.',
            ]
        ],
    ],
    
    /**
     * Output Formatting
     * How the blueprint should be formatted
     */
    'output_format' => [
        'format' => 'clean HTML with semantic tags (h2, h3, p, ul, li, img, strong). Use <img> tags for images, not Markdown syntax.',
        'tone' => 'professional, consultative,explanatory',
        'style' => 'detailed yet accessible, concepts are simple and practical',
        'include_examples' => true,
        'include_diagrams' => false, // Diagrams are generated separately
    ],
    
    /**
     * Additional Instructions
     * Extra guidance for the AI
     */
    'additional_instructions' => [
        'Use specific details from the conversation history when available.',
        'Observe a line return after every heading, end of each paragraph',
        'Avoid generic examples - personalize based on user\'s actual workflow.',
        'Include realistic timelines estimates where appropriate.',
        'Highlight quick wins and long-term strategic improvements.',
        'Use the user\'s own terminology and tools when mentioned.',
        'Observe scope, do not make statements outside of the scope of an individuals workflow unless relevant',
        'If statements are made that are quantitative eg. timeframes, include rationale',
        
        // Diagram is now included in the DISCOVER section guidelines above
    ],
    
    /**
     * Customization Notes
     * 
     * To customize the blueprint:
     * 
     * 1. CHANGE TECH STACK:
     *    Edit the 'tech_stack' array above
     * 
     * 2. ADD/REMOVE SECTIONS:
     *    Modify the 'blueprint_structure' array
     * 
     * 3. CHANGE TONE:
     *    Update 'output_format' settings
     * 
     * 4. ADD INSTRUCTIONS:
     *    Add items to 'additional_instructions'
     * 
     * After making changes, the AI will use this template
     * for all new blueprint generations.
     */
];
