<!-- Step 3: Dynamic Chat Interface -->
<div class="wizard-step" data-step="3">
  <h2>Let's dig a little deeper...</h2>
  <p class="step-description">I'm analyzing what you've told me. Let me ask you a few quick questions to make sure I get this right.</p>
  
  <!-- Chat Container -->
  <div id="chat-container">
    <div id="chat-messages"></div>
    <div id="chat-typing" style="display: none;">
      <div class="typing-indicator">
        <span></span>
        <span></span>
        <span></span>
      </div>
      <span class="typing-text">AI is thinking...</span>
    </div>
  </div>
  
  <!-- Chat Input -->
  <div class="chat-input-container">
    <label for="chat-input">Your Response</label>
    <textarea 
      id="chat-input" 
      name="chat-input" 
      rows="3" 
      placeholder="Type your answer here..."
    ></textarea>
    <button type="button" id="send-chat" class="btn">Send Message</button>
  </div>
  
  <!-- Hidden fields to store collected data -->
  <input type="hidden" id="collected-tools" name="tools" value="">
  <input type="hidden" id="collected-pain-points" name="pain_points" value="">
  <input type="hidden" id="chat-session-id" value="">
  <input type="hidden" id="chat-complete" value="false">
  
  <div class="wizard-buttons">
    <button type="button" class="btn-secondary btn wizard-back">← Back</button>
    <button type="submit" class="btn wizard-submit" id="chat-submit-btn" disabled>Generate Blueprint</button>
  </div>
</div>
