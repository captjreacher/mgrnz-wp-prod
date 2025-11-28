/**
 * AI Workflow Wizard - Production Version with Real AI Integration
 * 
 * This replaces the mock blueprint generation with actual AI API calls
 * Copy this code into your WPCode JavaScript snippet
 */

document.addEventListener("DOMContentLoaded", function () {
    console.log('Wizard JavaScript loaded');
    const form = document.getElementById("ai-wizard-form");
    if (!form) {
        console.error('Form not found!');
        return;
    }
    console.log('Form found:', form);

    const totalSteps = 5;
    let currentStep = 1;

    // DOM elements
    const formWrap = document.getElementById("ai-wizard-form-wrap");
    const blueprintWrap = document.getElementById("ai-wizard-blueprint-wrap");
    const statusEl = document.getElementById("ai-wizard-status");
    const decisionStatusEl = document.getElementById("ai-wizard-decision-status");
    const stepLabel = document.getElementById("ai-step-label");
    const stepCaption = document.getElementById("ai-step-caption");
    const progressFill = document.getElementById("ai-progress-fill");
    const prevBtn = document.getElementById("ai-prev-btn");
    const nextBtn = document.getElementById("ai-next-btn");
    const submitBtn = document.getElementById("ai-submit-btn");
    const summaryEl = document.getElementById("ai-wizard-summary");
    const markdownEl = document.getElementById("ai-wizard-blueprint-markdown");
    const subscribeBtn = document.getElementById("ai-wizard-subscribe");
    const consultBtn = document.getElementById("ai-wizard-consult");
    
    console.log('Buttons found:', {
        prevBtn: !!prevBtn,
        nextBtn: !!nextBtn,
        submitBtn: !!submitBtn
    });

    // Form inputs
    const goalInput = document.getElementById("goal");
    const workflowInput = document.getElementById("workflow");
    const toolsInput = document.getElementById("tools");
    const painInput = document.getElementById("pain_points");
    const emailInput = document.getElementById("email");

    // Review elements
    const reviewGoal = document.getElementById("review-goal");
    const reviewWorkflow = document.getElementById("review-workflow");
    const reviewTools = document.getElementById("review-tools");
    const reviewPain = document.getElementById("review-pain");

    const stepCaptions = {
        1: "Your goal",
        2: "Current workflow",
        3: "Tools you use",
        4: "Pain points",
        5: "Review & submit"
    };

    function updateReview() {
        if (reviewGoal) reviewGoal.textContent = goalInput.value.trim() || "Not set yet.";
        if (reviewWorkflow) reviewWorkflow.textContent = workflowInput.value.trim() || "Not set yet.";
        if (reviewTools) reviewTools.textContent = toolsInput.value.trim() || "Not set yet.";
        if (reviewPain) reviewPain.textContent = painInput.value.trim() || "Not set yet.";
    }

    function setStep(step) {
        currentStep = step;
        const steps = document.querySelectorAll(".mgrnz-ai-step");
        steps.forEach(function (el) {
            const s = parseInt(el.getAttribute("data-step"), 10);
            el.classList.toggle("active", s === currentStep);
        });

        if (stepLabel) {
            stepLabel.textContent = "Step " + currentStep + " of " + totalSteps;
        }
        if (stepCaption) {
            stepCaption.textContent = stepCaptions[currentStep] || "";
        }
        if (progressFill) {
            const progress = (currentStep / totalSteps) * 100;
            progressFill.style.width = progress + "%";
        }
        if (prevBtn) {
            prevBtn.style.visibility = currentStep === 1 ? "hidden" : "visible";
        }
        if (nextBtn && submitBtn) {
            nextBtn.style.display = currentStep === totalSteps ? "none" : "inline-flex";
            submitBtn.style.display = currentStep === totalSteps ? "inline-flex" : "none";
        }
        updateReview();
    }

    function validateCurrentStep() {
        if (!statusEl) return true;
        statusEl.textContent = "";
        statusEl.classList.remove("mgrnz-status-error");

        if (currentStep === 1 && !goalInput.value.trim()) {
            statusEl.textContent = "Give me at least a sentence about your goal.";
            statusEl.classList.add("mgrnz-status-error");
            return false;
        }
        if (currentStep === 2 && !workflowInput.value.trim()) {
            statusEl.textContent = "Describe your current workflow so I have something to improve.";
            statusEl.classList.add("mgrnz-status-error");
            return false;
        }
        // Step 5 is now review - no email validation needed
        return true;
    }

    function setLoading(isLoading) {
        if (!statusEl) return;
        if (isLoading) {
            statusEl.textContent = "Building your AI workflow blueprint…";
            statusEl.classList.remove("mgrnz-status-error");
        }
    }

    function showDecision(message) {
        if (!decisionStatusEl) return;
        decisionStatusEl.textContent = message;
    }

    function renderMarkdown(markdown) {
        // Simple markdown to HTML conversion
        let html = markdown
            .replace(/### (.*)/g, '<h3>$1</h3>')
            .replace(/## (.*)/g, '<h2>$1</h2>')
            .replace(/# (.*)/g, '<h1>$1</h1>')
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/\n\n/g, '</p><p>')
            .replace(/\n/g, '<br>');
        
        return '<p>' + html + '</p>';
    }

    // Event listeners
    if (prevBtn) {
        prevBtn.addEventListener("click", function (e) {
            e.preventDefault();
            if (currentStep > 1) {
                setStep(currentStep - 1);
            }
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener("click", function (e) {
            e.preventDefault();
            if (!validateCurrentStep()) return;
            if (currentStep < totalSteps) {
                setStep(currentStep + 1);
            }
        });
    }

    if (submitBtn) {
        submitBtn.addEventListener("click", function (e) {
            e.preventDefault();
            if (!validateCurrentStep()) return;

            setLoading(true);
            submitBtn.disabled = true;
            nextBtn && (nextBtn.disabled = true);
            prevBtn && (prevBtn.disabled = true);

            // Generate submission reference ID
            const timestamp = Date.now();
            const submissionRef = 'WIZ-' + timestamp.toString(36).toUpperCase();
            
            const payload = {
                goal: goalInput.value.trim(),
                workflow: workflowInput.value.trim(),
                tools: toolsInput.value.trim(),
                pain_points: painInput.value.trim(),
                submission_ref: submissionRef,
                timestamp: timestamp
            };
            
            // Store wizard data for quote page access
            localStorage.setItem('mgrnz_wizard_data', JSON.stringify(payload));

            updateReview();

            // Get nonce from WordPress (try multiple sources)
            const nonce = (typeof wpApiSettings !== 'undefined' && wpApiSettings.nonce) 
                || (typeof wp !== 'undefined' && wp.apiFetch && wp.apiFetch.nonceMiddleware && wp.apiFetch.nonceMiddleware.nonce)
                || '';
            
            // Call the real AI API endpoint
            fetch('/wp-json/mgrnz/v1/ai-workflow', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': nonce
                },
                body: JSON.stringify(payload)
            })
            .then(function(response) {
                // Debug: log the response
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers.get('content-type'));
                
                // Get the raw text first to see what we're actually receiving
                return response.text().then(function(text) {
                    console.log('Raw response:', text.substring(0, 500));
                    
                    if (!response.ok) {
                        throw new Error('HTTP error ' + response.status);
                    }
                    
                    // Try to parse as JSON
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        console.error('Response was:', text);
                        throw new Error('Invalid JSON response from server');
                    }
                });
            })
            .then(function(data) {
                if (data.success && data.blueprint) {
                    // Display the AI-generated blueprint (now HTML string)
                    if (summaryEl) {
                        summaryEl.textContent = "Here's your personalized AI workflow blueprint.";
                    }
                    if (markdownEl) {
                        // Blueprint is already HTML from the server
                        markdownEl.innerHTML = data.blueprint;
                    }
                    if (statusEl) {
                        const emailMsg = data.email_scheduled ? " A copy will be sent to your email." : "";
                        statusEl.textContent = "Done! Review your workflow below." + emailMsg;
                    }

                    // Show blueprint section
                    console.log('Showing blueprint...', {formWrap: !!formWrap, blueprintWrap: !!blueprintWrap});
                    if (formWrap && blueprintWrap) {
                        formWrap.style.display = "none";
                        blueprintWrap.style.display = "block";
                        console.log('Blueprint should now be visible!');
                    } else {
                        console.error('Elements not found!', {formWrap, blueprintWrap});
                    }
                } else {
                    throw new Error('Invalid response from server');
                }
            })
            .catch(function(error) {
                console.error('Blueprint generation error:', error);
                if (statusEl) {
                    statusEl.textContent = error.message || 'Unable to generate blueprint. Please try again.';
                    statusEl.classList.add("mgrnz-status-error");
                }
            })
            .finally(function() {
                submitBtn.disabled = false;
                nextBtn && (nextBtn.disabled = false);
                prevBtn && (prevBtn.disabled = false);
            });
        });
    }

    if (subscribeBtn) {
        subscribeBtn.addEventListener("click", function (e) {
            e.preventDefault();
            // Use the subscription modal instead of MailerLite waitlist
            if (window.mgrnzSubscriptionModal) {
                window.mgrnzSubscriptionModal.show();
            } else {
                console.warn("Subscription modal not available");
                // Fallback to MailerLite if subscription modal not loaded
                try {
                    if (window.ml) {
                        window.ml("show", "qyrDmy", true);
                    }
                } catch (err) {
                    console.warn("MailerLite not available:", err);
                }
            }
            showDecision("Nice. I'll keep you in the loop with practical AI updates.");
        });
    }

    if (consultBtn) {
        consultBtn.addEventListener("click", function (e) {
            e.preventDefault();
            const calendlyUrl = "https://calendly.com/mike-mikerobinson";
            if (window.Calendly && window.Calendly.initPopupWidget) {
                window.Calendly.initPopupWidget({ url: calendlyUrl });
            } else {
                window.open(calendlyUrl, "_blank");
            }
            showDecision("Consult booked or opened – I'll meet you there.");
        });
    }

    // Initialize
    setStep(1);
});
