<script>
<?php
// Include node templates for JavaScript use
include 'node-templates.php';
?>

var id = document.getElementById("drawflow");
const editor = new Drawflow(id);
editor.reroute = true;
editor.reroute_fix_curvature = true;
editor.force_first_input = false;

editor.start();

// Events!
editor.on('nodeCreated', function(id) {
  console.log("Node created " + id);
})

editor.on('nodeRemoved', function(id) {
  console.log("Node removed " + id);
})

editor.on('nodeSelected', function(id) {
  console.log("Node selected " + id);
})

editor.on('moduleCreated', function(name) {
  console.log("Module Created " + name);
})

editor.on('moduleChanged', function(name) {
  console.log("Module Changed " + name);
})

editor.on('connectionCreated', function(connection) {
  console.log('Connection created');
  console.log(connection);
})

editor.on('connectionRemoved', function(connection) {
  console.log('Connection removed');
  console.log(connection);
})

editor.on('nodeMoved', function(id) {
  console.log("Node moved " + id);
})

editor.on('zoom', function(zoom) {
  console.log('Zoom level ' + zoom);
})

editor.on('translate', function(position) {
  console.log('Translate x:' + position.x + ' y:'+ position.y);
})

editor.on('addReroute', function(id) {
  console.log("Reroute added " + id);
})

editor.on('removeReroute', function(id) {
  console.log("Reroute removed " + id);
})

/* DRAG EVENT */

/* Mouse and Touch Actions */

var elements = document.getElementsByClassName('drag-drawflow');
for (var i = 0; i < elements.length; i++) {
  elements[i].addEventListener('touchend', drop, false);
  elements[i].addEventListener('touchmove', positionMobile, false);
  elements[i].addEventListener('touchstart', drag, false );
}

var mobile_item_selec = '';
var mobile_last_move = null;

function positionMobile(ev) {
  mobile_last_move = ev;
}

function allowDrop(ev) {
   ev.preventDefault();
}

function drag(ev) {
  if (ev.type === "touchstart") {
    mobile_item_selec = ev.target.closest(".drag-drawflow").getAttribute('data-node');
  } else {
  ev.dataTransfer.setData("node", ev.target.getAttribute('data-node'));
  }
}

function drop(ev) {
  if (ev.type === "touchend") {
    var parentdrawflow = document.elementFromPoint( mobile_last_move.touches[0].clientX, mobile_last_move.touches[0].clientY).closest("#drawflow");
    if(parentdrawflow != null) {
      addNodeToDrawFlow(mobile_item_selec, mobile_last_move.touches[0].clientX, mobile_last_move.touches[0].clientY);
    }
    mobile_item_selec = '';
  } else {
    ev.preventDefault();
    var data = ev.dataTransfer.getData("node");
    addNodeToDrawFlow(data, ev.clientX, ev.clientY);
  }
}

function addNodeToDrawFlow(name, pos_x, pos_y) {
  if(editor.editor_mode === 'fixed') {
    return false;
  }
  pos_x = pos_x * ( editor.precanvas.clientWidth / (editor.precanvas.clientWidth * editor.zoom)) - (editor.precanvas.getBoundingClientRect().x * ( editor.precanvas.clientWidth / (editor.precanvas.clientWidth * editor.zoom)));
  pos_y = pos_y * ( editor.precanvas.clientHeight / (editor.precanvas.clientHeight * editor.zoom)) - (editor.precanvas.getBoundingClientRect().y * ( editor.precanvas.clientHeight / (editor.precanvas.clientHeight * editor.zoom)));

  switch (name) {
    case 'rich-text':
      var richTextTemplate = `
        <div>
          <div class="title-box">
            <i class="fas fa-edit"></i> Rich Text Editor
            <div class="ellipsis-menu" onclick="toggleEllipsisMenu(this)">
              <i class="fas fa-ellipsis-v"></i>
              <div class="ellipsis-dropdown">
                <div class="ellipsis-option" onclick="addSingleChoice(this)">Single Choice Question</div>
                <div class="ellipsis-option" onclick="addMultipleChoice(this)">Multiple Choice Question</div>
              </div>
            </div>
          </div>
          <div class="box">
            <div class="rich-text-content">
              <div class="rich-text-preview" id="preview-${Date.now()}">
                <p style="color: #666; font-style: italic;">Click "Edit Content" to add rich text...</p>
              </div>
              <textarea df-name class="rich-text-editor" style="display: none;"></textarea>
            </div>
            <div class="editor-toolbar">
              <button type="button" onclick="initRichTextEditor(this)" class="btn-edit"><i class="fas fa-edit"></i></button>
            </div>
            <div class="question-container" id="question-container-${Date.now()}">
              <!-- Questions will be added here -->
            </div>
          </div>
        </div>
      `;
      editor.addNode('rich-text', 1, 1, pos_x, pos_y, 'rich-text', {}, richTextTemplate);
      break;
    default:
  }
}

var transform = '';
function showpopup(e) {
  e.target.closest(".drawflow-node").style.zIndex = "9999";
  e.target.children[0].style.display = "block";
  transform = editor.precanvas.style.transform;
  editor.precanvas.style.transform = '';
  editor.precanvas.style.left = editor.canvas_x +'px';
  editor.precanvas.style.top = editor.canvas_y +'px';
  console.log(transform);
  editor.editor_mode = "fixed";
}

function closemodal(e) {
  e.target.closest(".drawflow-node").style.zIndex = "2";
  e.target.parentElement.parentElement.style.display  ="none";
  editor.precanvas.style.transform = transform;
    editor.precanvas.style.left = '0px';
    editor.precanvas.style.top = '0px';
   editor.editor_mode = "edit";
}

function changeModule(event) {
  var all = document.querySelectorAll(".menu ul li");
    for (var i = 0; i < all.length; i++) {
      all[i].classList.remove('selected');
    }
  event.target.classList.add('selected');
}

function changeMode(option) {
  if(option == 'lock') {
    lock.style.display = 'none';
    unlock.style.display = 'block';
  } else {
    lock.style.display = 'block';
    unlock.style.display = 'none';
  }
}

function exportJSON() {
    const jsonData = editor.export();

    const dataStr = JSON.stringify(jsonData, null, 4);
    const blob = new Blob([dataStr], { type: "application/json" });
    const url = URL.createObjectURL(blob);

    const a = document.createElement('a');
    a.href = url;
    a.download = 'export.json';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

// WYSIWYG Editor Functions
function initRichTextEditor(button) {
  const nodeElement = button.closest('.drawflow-node');
  const textarea = nodeElement.querySelector('.rich-text-editor');
  const nodeId = nodeElement.id.replace('node-', '');
  
  // Get existing content from textarea or preview
  let existingContent = textarea.value || '';
  if (!existingContent) {
    const preview = nodeElement.querySelector('.rich-text-preview');
    if (preview.innerHTML !== '<p style="color: #666; font-style: italic;">Click "Edit Content" to add rich text...</p>') {
      existingContent = preview.innerHTML;
    }
  }
  
  // Clean up the content if it's HTML
  if (existingContent && existingContent.includes('<')) {
    // It's HTML content, use as is
    existingContent = existingContent;
  } else if (existingContent && existingContent.trim()) {
    // It's plain text, wrap in paragraph
    existingContent = `<p>${existingContent}</p>`;
  }
  
  // Create modal for WYSIWYG editor
  const modal = document.createElement('div');
  modal.className = 'wysiwyg-modal';
  modal.innerHTML = `
    <div class="wysiwyg-modal-content">
      <div class="wysiwyg-header">
        <h3>Rich Text Editor</h3>
        <span class="wysiwyg-close" onclick="closeWysiwygModal(this)">&times;</span>
      </div>
      <div class="wysiwyg-body">
        <div id="wysiwyg-editor-${nodeId}">${existingContent}</div>
      </div>
      <div class="wysiwyg-footer">
        <button type="button" onclick="saveWysiwygContent(${nodeId})" class="btn-save">Save</button>
        <button type="button" onclick="closeWysiwygModal(this)" class="btn-cancel">Cancel</button>
      </div>
    </div>
  `;
  
  document.body.appendChild(modal);
  
  // Initialize Quill.js
  const quill = new Quill('#wysiwyg-editor-' + nodeId, {
    theme: 'snow',
    modules: {
      toolbar: [
        ['bold', 'italic', 'underline', 'strike'],
        ['blockquote', 'code-block'],
        [{ header: 1 }, { header: 2 }],
        [{ list: 'ordered' }, { list: 'bullet' }],
        [{ color: [] }, { background: [] }],
        [{ align: [] }],
        ['link', 'image'],
        ['clean']
      ]
    },
    formats: ['bold', 'italic', 'underline', 'strike', 'blockquote', 'code-block', 'header', 'list', 'bullet', 'color', 'background', 'align', 'link', 'image']
  });
  
  // Set initial content if it exists
  if (existingContent && existingContent.trim()) {
    quill.root.innerHTML = existingContent;
  }
}

function saveWysiwygContent(nodeId) {
  const quill = Quill.find('#wysiwyg-editor-' + nodeId);
  const content = quill.root.innerHTML;
  
  // Update the textarea in the node
  const nodeElement = document.getElementById(`node-${nodeId}`);
  const textarea = nodeElement.querySelector('.rich-text-editor');
  textarea.value = content;
  
  // Update the preview area
  const preview = nodeElement.querySelector('.rich-text-preview');
  if (content.trim()) {
    preview.innerHTML = content;
  } else {
    preview.innerHTML = '<p style="color: #666; font-style: italic;">Click "Edit Content" to add rich text...</p>';
  }
  
  // Update the node data
  const nodeData = { name: content };
  editor.updateNodeDataFromId(nodeId, nodeData);
  
  // Close modal and destroy editor
  closeWysiwygModal(document.querySelector('.wysiwyg-modal'));
}

function closeWysiwygModal(element) {
  const modal = element.closest('.wysiwyg-modal');
  if (!modal) return;
  
  const editorDiv = modal.querySelector('[id^="wysiwyg-editor-"]');
  if (editorDiv) {
    const editorId = editorDiv.id;
    
    // Destroy Quill.js instance
    const quill = Quill.find('#' + editorId);
    if (quill) {
      quill.destroy();
    }
  }
  
  // Remove modal
  modal.remove();
}

// Ellipsis Menu Functions
function toggleEllipsisMenu(ellipsisElement) {
  const dropdown = ellipsisElement.querySelector('.ellipsis-dropdown');
  dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

function addSingleChoice(optionElement) {
  const nodeElement = optionElement.closest('.drawflow-node');
  const questionContainer = nodeElement.querySelector('.question-container');
  const questionId = Date.now();
  
  const singleChoiceHTML = `
    <div class="question-item" id="question-${questionId}">
      <div class="question-header">
        <span class="question-type">Single Choice Question</span>
      </div>
      <div class="question-content">
        <div class="question-wysiwyg">
          <div class="question-preview" id="question-preview-${questionId}">
            <p style="color: #666; font-style: italic;">Click "Edit Question" to add question content...</p>
          </div>
          <textarea class="question-editor" style="display: none;"></textarea>
          <div class="question-toolbar">
            <button type="button" onclick="initQuestionEditor(${questionId})" class="btn-edit-question"><i class="fas fa-edit"></i></button>
          </div>
        </div>
        <div class="options-container">
          <div class="option-item">
            <input type="radio" name="single-${questionId}" df-option>
            <input type="text" placeholder="Option 1" df-option-label class="option-text">
          </div>
          <div class="option-item">
            <input type="radio" name="single-${questionId}" df-option>
            <input type="text" placeholder="Option 2" df-option-label class="option-text">
          </div>
        </div>
        <button type="button" onclick="addOption(this, 'single')" class="btn-add-option">+</button>
      </div>
    </div>
  `;
  
  questionContainer.insertAdjacentHTML('beforeend', singleChoiceHTML);
  closeEllipsisMenu(optionElement);
}

function addMultipleChoice(optionElement) {
  const nodeElement = optionElement.closest('.drawflow-node');
  const questionContainer = nodeElement.querySelector('.question-container');
  const questionId = Date.now();
  
  const multipleChoiceHTML = `
    <div class="question-item" id="question-${questionId}">
      <div class="question-header">
        <span class="question-type">Multiple Choice Question</span>
      </div>
      <div class="question-content">
        <div class="question-wysiwyg">
          <div class="question-preview" id="question-preview-${questionId}">
            <p style="color: #666; font-style: italic;">Click "Edit Question" to add question content...</p>
          </div>
          <textarea class="question-editor" style="display: none;"></textarea>
          <div class="question-toolbar">
            <button type="button" onclick="initQuestionEditor(${questionId})" class="btn-edit-question"><i class="fas fa-edit"></i></button>
          </div>
        </div>
        <div class="options-container">
          <div class="option-item">
            <input type="checkbox" df-option>
            <input type="text" placeholder="Option 1" df-option-label class="option-text">
          </div>
          <div class="option-item">
            <input type="checkbox" df-option>
            <input type="text" placeholder="Option 2" df-option-label class="option-text">
          </div>
        </div>
        <button type="button" onclick="addOption(this, 'multiple')" class="btn-add-option">+</button>
      </div>
    </div>
  `;
  
  questionContainer.insertAdjacentHTML('beforeend', multipleChoiceHTML);
  closeEllipsisMenu(optionElement);
}

function addOption(button, type) {
  const questionItem = button.closest('.question-item');
  const optionsContainer = questionItem.querySelector('.options-container');
  const questionId = questionItem.id.replace('question-', '');
  
  const optionHTML = `
    <div class="option-item">
      <input type="${type === 'single' ? 'radio' : 'checkbox'}" name="${type === 'single' ? 'single-' + questionId : 'multiple-' + questionId}" df-option>
      <input type="text" placeholder="New option" df-option-label class="option-text">
    </div>
  `;
  
  optionsContainer.insertAdjacentHTML('beforeend', optionHTML);
}

function removeQuestion(button) {
  const questionItem = button.closest('.question-item');
  questionItem.remove();
}

function closeEllipsisMenu(optionElement) {
  const dropdown = optionElement.closest('.ellipsis-dropdown');
  dropdown.style.display = 'none';
}

window.questionEditors = window.questionEditors || {};

function initQuestionEditor(questionId) {
  const questionElement = document.getElementById(`question-${questionId}`);
  const textarea = questionElement.querySelector('.question-editor');
  const preview = questionElement.querySelector('.question-preview');
  
  // Get existing content
  let existingContent = textarea.value || '';
  if (!existingContent) {
    if (preview.innerHTML !== '<p style="color: #666; font-style: italic;">Click "Edit Question" to add question content...</p>') {
      existingContent = preview.innerHTML;
    }
  }
  
  // Create modal for question WYSIWYG editor
  const modal = document.createElement('div');
  modal.className = 'wysiwyg-modal';
  modal.innerHTML = `
    <div class="wysiwyg-modal-content">
      <div class="wysiwyg-header">
        <h3>Question Editor</h3>
        <span class="wysiwyg-close" onclick="closeWysiwygModal(this)">&times;</span>
      </div>
      <div class="wysiwyg-body">
        <div id="question-wysiwyg-editor-${questionId}">${existingContent}</div>
      </div>
      <div class="wysiwyg-footer">
        <button type="button" onclick="saveQuestionContent(${questionId})" class="btn-save">Save</button>
        <button type="button" onclick="closeWysiwygModal(this)" class="btn-cancel">Cancel</button>
      </div>
    </div>
  `;
  
  document.body.appendChild(modal);
  
  // Initialize Quill.js for question editor
  const quill = new Quill('#question-wysiwyg-editor-' + questionId, {
    theme: 'snow',
    modules: {
      toolbar: [
        ['bold', 'italic', 'underline', 'strike'],
        ['blockquote', 'code-block'],
        [{ header: 1 }, { header: 2 }],
        [{ list: 'ordered' }, { list: 'bullet' }],
        [{ color: [] }, { background: [] }],
        [{ align: [] }],
        ['link', 'image'],
        ['clean']
      ]
    },
    formats: ['bold', 'italic', 'underline', 'strike', 'blockquote', 'code-block', 'header', 'list', 'bullet', 'color', 'background', 'align', 'link', 'image']
  });
  window.questionEditors[questionId] = quill;
  
  // Set initial content if it exists
  if (existingContent && existingContent.trim()) {
    quill.root.innerHTML = existingContent;
  }
}

function saveQuestionContent(questionId) {
  try {
    const quill = window.questionEditors[questionId];
    if (!quill) {
      console.error('Quill editor not found');
      return;
    }
    
    const content = quill.root.innerHTML;
    console.log('Saving question content:', content);
    
    // Update the textarea in the question
    const questionElement = document.getElementById(`question-${questionId}`);
    if (!questionElement) {
      console.error('Question element not found');
      return;
    }
    
    const textarea = questionElement.querySelector('.question-editor');
    if (textarea) {
      textarea.value = content;
    }
    
    // Update the preview area
    const preview = questionElement.querySelector('.question-preview');
    if (preview) {
      if (content.trim() && content !== '<p><br></p>') {
        preview.innerHTML = content;
      } else {
        preview.innerHTML = '<p style="color: #666; font-style: italic;">Click "Edit Question" to add question content...</p>';
      }
    }
    
    console.log('Question content saved successfully');
    
    // Close modal and destroy editor
    closeWysiwygModal(document.querySelector('.wysiwyg-modal'));
    
  } catch (error) {
    console.error('Error saving question content:', error);
    alert('Error saving question content. Please try again.');
  }
}

function initMessageEditor(messageId) {
  const messageElement = document.querySelector(`[id*="message-preview-${messageId}"]`).closest('.drawflow-node');
  const textarea = messageElement.querySelector('.message-editor');
  const preview = messageElement.querySelector('.message-preview');
  
  // Get existing content
  let existingContent = textarea.value || '';
  if (!existingContent) {
    if (preview.innerHTML !== '<p style="color: #666; font-style: italic;">Click "Edit Message" to add content...</p>') {
      existingContent = preview.innerHTML;
    }
  }
  
  // Create modal for message WYSIWYG editor
  const modal = document.createElement('div');
  modal.className = 'wysiwyg-modal';
  modal.innerHTML = `
    <div class="wysiwyg-modal-content">
      <div class="wysiwyg-header">
        <h3>Message Editor</h3>
        <span class="wysiwyg-close" onclick="closeWysiwygModal(this)">&times;</span>
      </div>
      <div class="wysiwyg-body">
        <div id="message-wysiwyg-editor-${messageId}">${existingContent}</div>
      </div>
      <div class="wysiwyg-footer">
        <button type="button" onclick="saveMessageContent(${messageId})" class="btn-save">Save</button>
        <button type="button" onclick="closeWysiwygModal(this)" class="btn-cancel">Cancel</button>
      </div>
    </div>
  `;
  
  document.body.appendChild(modal);
  
  // Initialize Quill.js for message editor
  const quill = new Quill('#message-wysiwyg-editor-' + messageId, {
    theme: 'snow',
    modules: {
      toolbar: [
        ['bold', 'italic', 'underline', 'strike'],
        ['blockquote', 'code-block'],
        [{ header: 1 }, { header: 2 }],
        [{ list: 'ordered' }, { list: 'bullet' }],
        [{ color: [] }, { background: [] }],
        [{ align: [] }],
        ['link', 'image'],
        ['clean']
      ]
    },
    formats: ['bold', 'italic', 'underline', 'strike', 'blockquote', 'code-block', 'header', 'list', 'bullet', 'color', 'background', 'align', 'link', 'image']
  });
  window.messageEditors = window.messageEditors || {};
  window.messageEditors[messageId] = quill;
  
  // Set initial content if it exists
  if (existingContent && existingContent.trim()) {
    quill.root.innerHTML = existingContent;
  }
}

function saveMessageContent(messageId) {
  try {
    const quill = window.messageEditors[messageId];
    if (!quill) {
      console.error('Quill editor not found');
      return;
    }
    
    const content = quill.root.innerHTML;
    console.log('Saving message content:', content);
    
    // Update the textarea in the message
    const messageElement = document.querySelector(`[id*="message-preview-${messageId}"]`).closest('.drawflow-node');
    if (!messageElement) {
      console.error('Message element not found');
      return;
    }
    
    const textarea = messageElement.querySelector('.message-editor');
    if (textarea) {
      textarea.value = content;
    }
    
    // Update the preview area
    const preview = messageElement.querySelector('.message-preview');
    if (preview) {
      if (content.trim() && content !== '<p><br></p>') {
        preview.innerHTML = content;
      } else {
        preview.innerHTML = '<p style="color: #666; font-style: italic;">Click "Edit Message" to add content...</p>';
      }
    }
    
    console.log('Message content saved successfully');
    
    // Close modal and destroy editor
    closeWysiwygModal(document.querySelector('.wysiwyg-modal'));
    
  } catch (error) {
    console.error('Error saving message content:', error);
    alert('Error saving message content. Please try again.');
  }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
  const ellipsisMenus = document.querySelectorAll('.ellipsis-menu');
  ellipsisMenus.forEach(menu => {
    const dropdown = menu.querySelector('.ellipsis-dropdown');
    if (!menu.contains(event.target)) {
      dropdown.style.display = 'none';
    }
  });
  
  // Close start options when clicking outside
  const startOptions = document.getElementById('start-options');
  const startButton = document.querySelector('.start-button');
  if (startOptions && startButton && !startButton.contains(event.target) && !startOptions.contains(event.target)) {
    startOptions.style.display = 'none';
  }
});

// Start Button Functions
function showStartOptions() {
  const startOptions = document.getElementById('start-options');
  startOptions.style.display = startOptions.style.display === 'block' ? 'none' : 'block';
}

<?php
// Generate node creation functions dynamically
foreach ($nodeTypes as $type => $config) {
    $functionName = 'add' . ucfirst(str_replace('-', '', $type)) . 'Node';
    $nodeType = str_replace('-', '-', $type);
    $inputs = $config['inputs'];
    $outputs = $config['outputs'];
    
    echo "function {$functionName}() {\n";
    echo "  const centerX = editor.precanvas.clientWidth / 2;\n";
    echo "  const startY = 100;\n";
    echo "  \n";
    echo "  const template = `\n";
    
    // Generate template based on node type
    switch($type) {
        case 'start':
            echo "    <div>\n";
            echo "      <div class=\"ellipsis-menu\" onclick=\"toggleEllipsisMenu(this)\">\n";
            echo "        <i class=\"fas fa-ellipsis-v\"></i>\n";
            echo "        <div class=\"ellipsis-dropdown\">\n";
            echo "          " . str_replace('"', '\"', generateEllipsisMenu()) . "\n";
            echo "        </div>\n";
            echo "      </div>\n";
            echo "      <div class=\"box start-action-box\">\n";
            echo "        <div class=\"node-header\">\n";
            echo "          <i class=\"{$config['icon']}\"></i>\n";
            echo "          <span>{$config['name']}</span>\n";
            echo "        </div>\n";
            echo "        <div class=\"node-content\">\n";
            echo "          <p>This is the entry point of your chat flow. All flows should begin with a Start Action.</p>\n";
            echo "        </div>\n";
            echo "      </div>\n";
            echo "    </div>\n";
            break;
            
        case 'end':
            echo "    <div>\n";
            echo "      <div class=\"ellipsis-menu\" onclick=\"toggleEllipsisMenu(this)\">\n";
            echo "        <i class=\"fas fa-ellipsis-v\"></i>\n";
            echo "        <div class=\"ellipsis-dropdown\">\n";
            echo "          " . str_replace('"', '\"', generateEllipsisMenu()) . "\n";
            echo "        </div>\n";
            echo "      </div>\n";
            echo "      <div class=\"box end-action-box\">\n";
            echo "        <div class=\"node-header\">\n";
            echo "          <i class=\"{$config['icon']}\"></i>\n";
            echo "          <span>{$config['name']}</span>\n";
            echo "        </div>\n";
            echo "        <div class=\"node-content\">\n";
            echo "          <div class=\"end-options\">\n";
            echo "            <label><input type=\"checkbox\" name=\"auto-close\" value=\"true\"> Auto close chat</label>\n";
            echo "            <label><input type=\"checkbox\" name=\"save-session\" value=\"true\"> Save session data</label>\n";
            echo "            <label><input type=\"checkbox\" name=\"show-feedback\" value=\"true\"> Show feedback form</label>\n";
            echo "          </div>\n";
            echo "        </div>\n";
            echo "      </div>\n";
            echo "    </div>\n";
            break;
            
        case 'single':
        case 'multiple':
            $questionId = '${Date.now()}';
            echo "    <div>\n";
            echo "      <div class=\"ellipsis-menu\" onclick=\"toggleEllipsisMenu(this)\">\n";
            echo "        <i class=\"fas fa-ellipsis-v\"></i>\n";
            echo "        <div class=\"ellipsis-dropdown\">\n";
            echo "          " . str_replace('"', '\"', generateEllipsisMenu()) . "\n";
            echo "        </div>\n";
            echo "      </div>\n";
            echo "      <div class=\"box\">\n";
            echo "        <div class=\"question-container\" id=\"question-container-{$questionId}\">\n";
            echo "          " . str_replace('"', '\"', generateQuestionTemplate($type, $questionId)) . "\n";
            echo "        </div>\n";
            echo "      </div>\n";
            echo "    </div>\n";
            break;
            
        case 'slider':
            $questionId = '${Date.now()}';
            echo "    <div>\n";
            echo "      <div class=\"ellipsis-menu\" onclick=\"toggleEllipsisMenu(this)\">\n";
            echo "        <i class=\"fas fa-ellipsis-v\"></i>\n";
            echo "        <div class=\"ellipsis-dropdown\">\n";
            echo "          " . str_replace('"', '\"', generateEllipsisMenu()) . "\n";
            echo "        </div>\n";
            echo "      </div>\n";
            echo "      <div class=\"box\">\n";
            echo "        <div class=\"question-container\" id=\"question-container-{$questionId}\">\n";
            echo "          " . str_replace('"', '\"', generateSliderTemplate($questionId)) . "\n";
            echo "        </div>\n";
            echo "      </div>\n";
            echo "    </div>\n";
            break;
            
        case 'message':
            $messageId = '${Date.now()}';
            echo "    <div>\n";
            echo "      <div class=\"ellipsis-menu\" onclick=\"toggleEllipsisMenu(this)\">\n";
            echo "        <i class=\"fas fa-ellipsis-v\"></i>\n";
            echo "        <div class=\"ellipsis-dropdown\">\n";
            echo "          " . str_replace('"', '\"', generateEllipsisMenu()) . "\n";
            echo "        </div>\n";
            echo "      </div>\n";
            echo "      <div class=\"box message-box\">\n";
            echo "        <div class=\"node-header\">\n";
            echo "          <i class=\"{$config['icon']}\"></i>\n";
            echo "          <span>{$config['name']}</span>\n";
            echo "        </div>\n";
            echo "        <div class=\"node-content\">\n";
            echo "          " . str_replace('"', '\"', generateMessageTemplate($messageId)) . "\n";
            echo "        </div>\n";
            echo "      </div>\n";
            echo "    </div>\n";
            break;
            
        default:
            // Generic template for other node types
            echo "    <div>\n";
            echo "      <div class=\"ellipsis-menu\" onclick=\"toggleEllipsisMenu(this)\">\n";
            echo "        <i class=\"fas fa-ellipsis-v\"></i>\n";
            echo "        <div class=\"ellipsis-dropdown\">\n";
            echo "          " . str_replace('"', '\"', generateEllipsisMenu()) . "\n";
            echo "        </div>\n";
            echo "      </div>\n";
            echo "      <div class=\"box {$type}-box\">\n";
            echo "        <div class=\"node-header\">\n";
            echo "          <i class=\"{$config['icon']}\"></i>\n";
            echo "          <span>{$config['name']}</span>\n";
            echo "        </div>\n";
            echo "        <div class=\"node-content\">\n";
            echo "          <p>Configure {$config['name']} settings here.</p>\n";
            echo "        </div>\n";
            echo "      </div>\n";
            echo "    </div>\n";
            break;
    }
    
    echo "  `;\n";
    echo "  \n";
    echo "  editor.addNode('{$nodeType}', {$inputs}, {$outputs}, centerX, startY, '{$nodeType}', {}, template);\n";
    echo "  hideStartOptions();\n";
    echo "}\n\n";
}
?>

function addNextNode(optionElement, type) {
  const currentNode = optionElement.closest('.drawflow-node');
  const currentNodeRect = currentNode.getBoundingClientRect();
  const canvasRect = editor.precanvas.getBoundingClientRect();
  
  // Calculate position below current node
  const nextX = (currentNodeRect.left + currentNodeRect.width / 2 - canvasRect.left) / editor.zoom;
  const nextY = (currentNodeRect.bottom - canvasRect.top + 50) / editor.zoom; // 50px gap
  
  let nextNodeTemplate = '';
  let nodeType = '';
  let inputs = 1;
  let outputs = 1;
  
  // Create template based on node type
  switch(type) {
    case 'single':
    case 'multiple':
      nextNodeTemplate = `
        <div>
          <div class="ellipsis-menu" onclick="toggleEllipsisMenu(this)">
            <i class="fas fa-ellipsis-v"></i>
            <div class="ellipsis-dropdown">
              <?php echo str_replace('"', '\"', generateEllipsisMenu()); ?>
            </div>
          </div>
          <div class="box">
            <div class="question-container" id="question-container-${Date.now()}">
              <?php echo str_replace('"', '\"', generateQuestionTemplate('single', '${Date.now()}')); ?>
            </div>
          </div>
        </div>
      `;
      nodeType = `${type}-choice-next`;
      break;
      
    case 'slider':
      nextNodeTemplate = `
        <div>
          <div class="ellipsis-menu" onclick="toggleEllipsisMenu(this)">
            <i class="fas fa-ellipsis-v"></i>
            <div class="ellipsis-dropdown">
              <?php echo str_replace('"', '\"', generateEllipsisMenu()); ?>
            </div>
          </div>
          <div class="box">
            <div class="question-container" id="question-container-${Date.now()}">
              <?php echo str_replace('"', '\"', generateSliderTemplate('${Date.now()}')); ?>
            </div>
          </div>
        </div>
      `;
      nodeType = 'slider-question-next';
      break;
      
    case 'message':
      nextNodeTemplate = `
        <div>
          <div class="ellipsis-menu" onclick="toggleEllipsisMenu(this)">
            <i class="fas fa-ellipsis-v"></i>
            <div class="ellipsis-dropdown">
              <?php echo str_replace('"', '\"', generateEllipsisMenu()); ?>
            </div>
          </div>
          <div class="box message-box">
            <div class="node-header">
              <i class="fas fa-comment"></i>
              <span>Message</span>
            </div>
            <div class="node-content">
              <?php echo str_replace('"', '\"', generateMessageTemplate('${Date.now()}')); ?>
            </div>
          </div>
        </div>
      `;
      nodeType = 'message-next';
      break;
      
    default:
      // Default to single choice question
      nextNodeTemplate = `
        <div>
          <div class="ellipsis-menu" onclick="toggleEllipsisMenu(this)">
            <i class="fas fa-ellipsis-v"></i>
            <div class="ellipsis-dropdown">
              <?php echo str_replace('"', '\"', generateEllipsisMenu()); ?>
            </div>
          </div>
          <div class="box">
            <div class="question-container" id="question-container-${Date.now()}">
              <?php echo str_replace('"', '\"', generateQuestionTemplate('single', '${Date.now()}')); ?>
            </div>
          </div>
        </div>
      `;
      nodeType = 'single-choice-next';
      break;
  }
  
  // Get current node ID before adding new node
  const currentNodeId = currentNode.id.replace('node-', '');
  
  // Add the new node
  const newNodeId = editor.addNode(nodeType, inputs, outputs, nextX, nextY, nodeType, {}, nextNodeTemplate);
  
  // Wait a bit for the node to be fully created, then create connection
  setTimeout(() => {
    try {
      // Create connection from current node to new node
      editor.addConnection(currentNodeId, 0, newNodeId, 0);
      console.log(`Connection created from node ${currentNodeId} to node ${newNodeId}`);
    } catch (error) {
      console.error('Error creating connection:', error);
      // Try alternative connection method
      try {
        editor.addConnection(currentNodeId, 0, newNodeId, 0);
        console.log(`Alternative connection created from node ${currentNodeId} to node ${newNodeId}`);
      } catch (altError) {
        console.error('Alternative connection also failed:', altError);
        // Try manual connection creation
        try {
          const connection = {
            id: `conn_${currentNodeId}_${newNodeId}`,
            output_id: currentNodeId,
            output_class: 0,
            input_id: newNodeId,
            input_class: 0
          };
          editor.drawflow.drawflow[editor.module].data[connection.id] = connection;
          editor.updateConnectionNodes(`node-${currentNodeId}`);
          editor.updateConnectionNodes(`node-${newNodeId}`);
          console.log(`Manual connection created from node ${currentNodeId} to node ${newNodeId}`);
        } catch (manualError) {
          console.error('Manual connection also failed:', manualError);
        }
      }
    }
  }, 100);
  
  closeEllipsisMenu(optionElement);
}

function removeNode(optionElement) {
  console.log('removeNode called with:', optionElement);
  
  // Find the node element
  const nodeElement = optionElement.closest('.drawflow-node');
  console.log('Found node element:', nodeElement);
  
  if (!nodeElement) {
    console.error('Could not find node element');
    return;
  }
  
  const nodeId = nodeElement.id.replace('node-', '');
  console.log('Node ID to remove:', nodeId);
  
  try {
    editor.removeNodeId('node-' + nodeId);
    console.log('Node removed successfully');
  } catch (error) {
    console.error('Error removing node:', error);
  }
  
  closeEllipsisMenu(optionElement);
}

function hideStartOptions() {
  const startOptions = document.getElementById('start-options');
  startOptions.style.display = 'none';
}

// Load and Save functions
function loadFlow() {
  const savedData = localStorage.getItem('drawflow-data');
  if (savedData) {
    try {
      const data = JSON.parse(savedData);
      editor.import(data);
      console.log('Flow loaded successfully');
    } catch (error) {
      console.error('Error loading flow:', error);
      alert('Error loading flow data');
    }
  } else {
    alert('No saved flow data found');
  }
}

function saveFlow() {
  try {
    const data = editor.export();
    localStorage.setItem('drawflow-data', JSON.stringify(data));
    console.log('Flow saved successfully');
  } catch (error) {
    console.error('Error saving flow:', error);
    alert('Error saving flow data');
  }
}

// Auto-save every 30 seconds
setInterval(saveFlow, 30000);

</script> 