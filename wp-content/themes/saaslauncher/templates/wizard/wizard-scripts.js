// AI Workflow Wizard JavaScript
console.log("Wizard script loaded");

document.addEventListener("DOMContentLoaded", function () {
  console.log("DOM ready");

  let currentStep = 1;
  const totalSteps = 3;
  let chatSessionId = null;
  let chatComplete = false;

  const form = document.getElementById("wizard-form");
  const progressBar = document.getElementById("progress-bar");
  const currentStepEl = document.getElementById("current-step");
  const steps = document.querySelectorAll(".wizard-step");
  const nextBtns = document.querySelectorAll(".wizard-next");
  const backBtns = document.querySelectorAll(".wizard-back");

  console.log("Elements found:", { form: !!form, nextBtns: nextBtns.length, steps: steps.length });

  function setStep(step) {
    console.log("Setting step to:", step);
    currentStep = step;
    steps.forEach(function (el) {
      const s = parseInt(el.getAttribute("data-step"));
      el.classList.toggle("active", s === currentStep);
    });
    if (currentStepEl) currentStepEl.textContent = currentStep;
    if (progressBar) progressBar.style.width = ((currentStep / totalSteps) * 100) + "%";

    // Initialize chat when reaching step 3
    if (step === 3 && !chatSessionId) {
      console.log("Initializing chat...");
      setTimeout(initChat, 500);
    }
  }

  function validate() {
    if (currentStep === 1) {
      const goal = document.getElementById("goal");
      if (!goal || !goal.value.trim()) {
        alert("Please describe the problem.");
        return false;
      }
    }
    if (currentStep === 2) {
      const workflow = document.getElementById("workflow");
      if (!workflow || !workflow.value.trim()) {
        alert("Please describe your vision.");
        return false;
      }
    }
    return true;
  }

  nextBtns.forEach(function (btn) {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      console.log("Next clicked, step:", currentStep);
      if (validate() && currentStep < totalSteps) {
        setStep(currentStep + 1);
      }
    });
  });

  backBtns.forEach(function (btn) {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      if (currentStep > 1) {
        setStep(currentStep - 1);
      }
    });
  });

  // Chat functions
  function initChat() {
    const goal = document.getElementById("goal").value.trim();
    const workflow = document.getElementById("workflow").value.trim();

    console.log("Starting chat session...");

    fetch(window.wpApiSettings.root + "mgrnz/v1/start-chat", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ goal: goal, workflow: workflow })
    })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        console.log("Chat response:", data);
        if (data.session_id) {
          chatSessionId = data.session_id;
          document.getElementById("chat-session-id").value = chatSessionId;
          const aiResponse = data.message || data.assistant_response || data.response;
          if (aiResponse) {
            addChatMessage(aiResponse, "ai");
          }
        }
      })
      .catch(function (err) {
        console.error("Chat init error:", err);
        addChatMessage("Sorry, I had trouble starting. Please refresh and try again.", "ai");
      });
  }

  function addChatMessage(text, sender) {
    const container = document.getElementById("chat-messages");
    if (!container) return;
    const msg = document.createElement("div");
    msg.className = "chat-message chat-" + sender;
    msg.textContent = text;
    container.appendChild(msg);
    container.scrollTop = container.scrollHeight;
  }

  function showTyping() {
    const typing = document.getElementById("chat-typing");
    if (typing) typing.style.display = "flex";
  }

  function hideTyping() {
    const typing = document.getElementById("chat-typing");
    if (typing) typing.style.display = "none";
  }

  const sendBtn = document.getElementById("send-chat");
  const chatInput = document.getElementById("chat-input");

  if (sendBtn) {
    sendBtn.addEventListener("click", function () {
      const msg = chatInput.value.trim();
      if (!msg || !chatSessionId) {
        console.log("Cannot send - msg:", !!msg, "session:", !!chatSessionId);
        return;
      }

      console.log("Sending message:", msg);
      addChatMessage(msg, "user");
      chatInput.value = "";
      showTyping();

      fetch(window.wpApiSettings.root + "mgrnz/v1/chat-message", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ session_id: chatSessionId, message: msg })
      })
        .then(function (res) { return res.json(); })
        .then(function (data) {
          console.log("Chat response:", data);
          hideTyping();
          const aiResponse = data.response || data.assistant_response || data.message;
          if (aiResponse) {
            addChatMessage(aiResponse, "ai");

            // Check for completion phrase in response as fallback
            if (aiResponse.includes("Generate Blueprint") || aiResponse.includes("click the") || aiResponse.includes("blueprint now")) {
              chatComplete = true;
              const submitBtn = document.getElementById("chat-submit-btn");
              if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.style.opacity = "1";
                submitBtn.style.cursor = "pointer";
              }
            }
          }
          if (data.complete) {
            chatComplete = true;
            const submitBtn = document.getElementById("chat-submit-btn");
            if (submitBtn) {
              submitBtn.disabled = false;
              submitBtn.style.opacity = "1";
              submitBtn.style.cursor = "pointer";
            }
            if (data.collected_data) {
              if (data.collected_data.tools) {
                document.getElementById("collected-tools").value = data.collected_data.tools;
              }
              if (data.collected_data.pain_points) {
                document.getElementById("collected-pain-points").value = data.collected_data.pain_points;
              }
            }
          }
        })
        .catch(function (err) {
          hideTyping();
          console.error("Chat error:", err);
          addChatMessage("Sorry, I had trouble with that. Please try again.", "ai");
        });
    });
  }

  if (chatInput) {
    chatInput.addEventListener("keypress", function (e) {
      if (e.key === "Enter" && !e.shiftKey) {
        e.preventDefault();
        sendBtn.click();
      }
    });
  }

  setStep(1);
  // Handle form submission (Generate Blueprint)
  if (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      console.log("Form submitted, current step:", currentStep);

      if (currentStep === 3) {
        handleBlueprintGeneration();
      }
    });
  }

  function handleBlueprintGeneration() {
    console.log("Generating blueprint...");

    // Show progress animation
    const progressAnimation = document.getElementById("progress-animation");
    const progressMessages = document.getElementById("progress-messages");
    const progressFill = document.getElementById("progress-fill");

    if (form) form.style.display = "none";
    if (progressAnimation) {
      progressAnimation.style.display = "block";
      // Force reflow
      void progressAnimation.offsetWidth;
      progressAnimation.classList.add("show");
      progressAnimation.style.opacity = "1";
    }

    // Collect data
    const goal = document.getElementById("goal").value;
    const workflow = document.getElementById("workflow").value;
    const tools = document.getElementById("collected-tools").value;
    const painPoints = document.getElementById("collected-pain-points").value;
    const sessionId = document.getElementById("chat-session-id").value;

    // Simulate progress messages
    const messages = [
      "Analyzing your workflow...",
      "Identifying automation opportunities...",
      "Drafting your custom blueprint..."
    ];

    let msgIdx = 0;
    if (progressMessages) {
      progressMessages.innerHTML = `<div class="progress-message active">${messages[0]}</div>`;
    }

    const msgInterval = setInterval(() => {
      if (progressMessages && messages[msgIdx]) {
        progressMessages.innerHTML = `<div class="progress-message active">${messages[msgIdx]}</div>`;
        if (progressFill) progressFill.style.width = ((msgIdx + 1) / messages.length * 100) + "%";
        msgIdx++;
        if (msgIdx >= messages.length) msgIdx = 0; // Loop messages if it takes too long
      }
    }, 2000);

    // Call API
    fetch(window.wpApiSettings.root + "mgrnz/v1/ai-workflow", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Bypass-Cache": "true" // Always bypass cache for chat-based submissions
      },
      body: JSON.stringify({
        goal: goal,
        workflow: workflow,
        tools: tools || "Not specified",
        pain_points: painPoints || "Not specified",
        session_id: sessionId,
        bypass_cache: true // Ensure fresh generation with chat context
      })
    })
      .then(res => res.json())
      .then(data => {
        clearInterval(msgInterval);
        if (data.success) {
          // Save to localStorage for the subscribe/download page
          localStorage.setItem('mgrnz_blueprint_download', data.blueprint);
          console.log('[WIZARD] Blueprint saved to localStorage after API response:', {
            length: data.blueprint ? data.blueprint.length : 0,
            preview: data.blueprint ? data.blueprint.substring(0, 100) + '...' : 'EMPTY',
            hasContent: !!data.blueprint
          });

          // Save wizard data including submission ref
          const wizardData = {
            goal: goal,
            workflow: workflow,
            submission_ref: data.submission_id || data.submission_ref || sessionId
          };
          localStorage.setItem('mgrnz_wizard_data', JSON.stringify(wizardData));

          // Show blueprint
          const blueprintSection = document.getElementById("blueprint-section");
          const blueprintContent = document.getElementById("blueprint-content");
          const completionScreen = document.getElementById("completion-screen");

          if (progressAnimation) progressAnimation.style.display = "none";

          if (blueprintContent) blueprintContent.innerHTML = data.blueprint;
          if (blueprintSection) {
            blueprintSection.style.display = "block";
            void blueprintSection.offsetWidth;
            blueprintSection.classList.add("show");
            blueprintSection.style.opacity = "1";
          }
          if (completionScreen) {
            completionScreen.style.display = "block";
            void completionScreen.offsetWidth;
            completionScreen.classList.add("show");
            completionScreen.style.opacity = "1";
          }
        } else {
          console.error("Blueprint generation failed:", data);
          alert("Error generating blueprint: " + (data.message || "Unknown error"));
          if (form) form.style.display = "block";
          if (progressAnimation) progressAnimation.style.display = "none";
        }
      })
      .catch(err => {
        clearInterval(msgInterval);
        console.error("Blueprint generation error:", err);
        alert("Sorry, there was an error generating your blueprint. Please try again.");
        if (form) form.style.display = "block";
        if (progressAnimation) progressAnimation.style.display = "none";
      });
  }

  // Handle Download Blueprint button click
  const downloadBtn = document.getElementById("btn-download-blueprint");
  if (downloadBtn) {
    downloadBtn.addEventListener("click", function () {
      console.log("Download blueprint clicked");
      // Save blueprint HTML to localStorage so it can be downloaded as PDF after subscription
      const blueprintContent = document.getElementById("blueprint-content");
      if (blueprintContent) {
        const blueprintHTML = blueprintContent.innerHTML;
        if (blueprintHTML && blueprintHTML.trim().length > 0) {
          localStorage.setItem('mgrnz_blueprint_download', blueprintHTML);
          console.log('[WIZARD] Blueprint re-saved from DOM on download click:', {
            length: blueprintHTML.length,
            preview: blueprintHTML.substring(0, 100) + '...',
            elementFound: true
          });
        } else {
          console.warn('[WIZARD] Blueprint content is empty! Not overwriting localStorage.');
          console.warn('[WIZARD] Current localStorage value:', localStorage.getItem('mgrnz_blueprint_download'));
        }
      } else {
        console.error('[WIZARD] Blueprint content element not found!');
      }
      // Navigate to subscribe page
      window.location.href = '/wizard-subscribe-page';
    });
  }

  console.log("Wizard initialized");

  // --- Auto-Test Feature ---
  // Adds a hidden button to quickly test the wizard flow
  const autoTestBtn = document.createElement('button');
  autoTestBtn.textContent = '? Auto-Test';
  autoTestBtn.style.cssText = 'position: fixed; bottom: 10px; right: 10px; z-index: 9999; background: #333; color: #fff; border: none; padding: 8px 12px; font-size: 12px; cursor: pointer; opacity: 0.5; border-radius: 4px; transition: opacity 0.3s;';
  autoTestBtn.onmouseover = () => autoTestBtn.style.opacity = '1';
  autoTestBtn.onmouseout = () => autoTestBtn.style.opacity = '0.5';
  autoTestBtn.title = 'Fill test data and generate blueprint automatically';
  document.body.appendChild(autoTestBtn);

  autoTestBtn.addEventListener('click', function () {
    console.log('? Auto-Test started...');
    autoTestBtn.textContent = '? Running...';
    autoTestBtn.disabled = true;

    // Step 1: Fill Data
    const goalInput = document.getElementById('goal');
    const workflowInput = document.getElementById('workflow');

    if (goalInput) goalInput.value = "I want to automate my client onboarding process.";
    if (workflowInput) workflowInput.value = "Currently, when a new client signs up, I manually send them a welcome email, create a folder in my cloud storage, and add them to my project management system. It takes about 30 minutes per client.";

    console.log('? Data filled');

    // Step 2: Advance to Chat
    // Click Next button for Step 1
    const nextBtn1 = document.querySelector('.wizard-step[data-step="1"] .wizard-next');
    if (nextBtn1) {
      nextBtn1.click();
      console.log('? Clicked Next (Step 1)');
    }

    // Wait for transition then Click Next for Step 2 (if it exists/is active)
    setTimeout(() => {
      const nextBtn2 = document.querySelector('.wizard-step[data-step="2"] .wizard-next');
      // Only click if Step 2 is active/visible
      if (nextBtn2 && nextBtn2.offsetParent !== null) {
        nextBtn2.click();
        console.log('? Clicked Next (Step 2)');
      }

      // Step 3: Wait for Chat Init & Generate
      console.log('? Waiting for chat initialization...');

      let attempts = 0;
      const checkChat = setInterval(() => {
        attempts++;
        // Check if session ID is set (meaning chat initialized)
        const sessionIdField = document.getElementById("chat-session-id");
        const submitBtn = document.getElementById("chat-submit-btn");

        if (sessionIdField && sessionIdField.value && submitBtn) {
          clearInterval(checkChat);
          console.log('? Chat initialized with Session ID:', sessionIdField.value);

          // Enable Generate Button
          submitBtn.disabled = false;
          submitBtn.style.opacity = "1";
          submitBtn.style.cursor = "pointer";

          // Trigger Generation
          console.log('? Triggering blueprint generation...');
          submitBtn.click();

          autoTestBtn.textContent = '? Done!';
          setTimeout(() => autoTestBtn.remove(), 2000);
        } else if (attempts > 20) { // 10 seconds timeout
          clearInterval(checkChat);
          console.error('? Auto-Test timed out waiting for chat');
          autoTestBtn.textContent = '? Failed';
          autoTestBtn.disabled = false;
        }
      }, 500);

    }, 600);
  });
});


// Deployment trigger - 2025-11-28 16:36:37

// Force deploy: 2025-11-28 16:53:42
