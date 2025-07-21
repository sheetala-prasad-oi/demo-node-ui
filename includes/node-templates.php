<?php
// Node template functions for reusability

function generateEllipsisMenu() {
    global $nodeTypes;
    $menu = '';
    foreach ($nodeTypes as $type => $config) {
        $menu .= '<div class="ellipsis-option" onclick="addNextNode(this, \'' . $type . '\')">' . $config['name'] . '</div>';
    }
    $menu .= '<div class="ellipsis-option" onclick="event.stopPropagation(); removeNode(this)">Remove Node</div>';
    return $menu;
}

function generateQuestionTemplate($type, $questionId) {
    $inputType = ($type === 'single') ? 'radio' : 'checkbox';
    $nameAttr = ($type === 'single') ? "name=\"{$type}-{$questionId}\"" : '';
    
    return '
        <div class="question-item" id="question-' . $questionId . '">
            <div class="question-header">
                <span class="question-type">' . ucfirst($type) . ' Choice Question</span>
            </div>
            <div class="question-content">
                <div class="question-wysiwyg">
                    <div class="question-preview" id="question-preview-' . $questionId . '">
                        <p style="color: #666; font-style: italic;">Click "Edit Question" to add question content...</p>
                    </div>
                    <textarea class="question-editor" style="display: none;"></textarea>
                    <div class="question-toolbar">
                        <button type="button" onclick="initQuestionEditor(' . $questionId . ')" class="btn-edit-question"><i class="fas fa-edit"></i></button>
                    </div>
                </div>
                <div class="options-container">
                    <div class="option-item">
                        <input type="' . $inputType . '" ' . $nameAttr . ' df-option>
                        <input type="text" placeholder="Option 1" df-option-label class="option-text">
                    </div>
                    <div class="option-item">
                        <input type="' . $inputType . '" ' . $nameAttr . ' df-option>
                        <input type="text" placeholder="Option 2" df-option-label class="option-text">
                    </div>
                </div>
                <button type="button" onclick="addOption(this, \'' . $type . '\')" class="btn-add-option">+</button>
            </div>
        </div>';
}

function generateSliderTemplate($questionId) {
    return '
        <div class="question-item" id="question-' . $questionId . '">
            <div class="question-header">
                <span class="question-type">Slider Question</span>
            </div>
            <div class="question-content">
                <div class="question-wysiwyg">
                    <div class="question-preview" id="question-preview-' . $questionId . '">
                        <p style="color: #666; font-style: italic;">Click "Edit Question" to add question content...</p>
                    </div>
                    <textarea class="question-editor" style="display: none;"></textarea>
                    <div class="question-toolbar">
                        <button type="button" onclick="initQuestionEditor(' . $questionId . ')" class="btn-edit-question"><i class="fas fa-edit"></i></button>
                    </div>
                </div>
                <div class="slider-config">
                    <div class="slider-range">
                        <label>Range:</label>
                        <input type="number" placeholder="Min" class="slider-min" value="1">
                        <span>to</span>
                        <input type="number" placeholder="Max" class="slider-max" value="10">
                    </div>
                    <div class="slider-step">
                        <label>Step:</label>
                        <input type="number" placeholder="Step" class="slider-step-value" value="1">
                    </div>
                </div>
            </div>
        </div>';
}

function generateMessageTemplate($messageId) {
    return '
        <div class="message-wysiwyg">
            <div class="message-preview" id="message-preview-' . $messageId . '">
                <p style="color: #666; font-style: italic;">Click "Edit Message" to add content...</p>
            </div>
            <textarea class="message-editor" style="display: none;"></textarea>
            <div class="message-toolbar">
                <button type="button" onclick="initMessageEditor(' . $messageId . ')" class="btn-edit-message"><i class="fas fa-edit"></i></button>
            </div>
        </div>
        <div class="message-options">
            <label><input type="checkbox" name="read-more" value="true"> Show "Read More" option</label>
            <div class="replacement-tags">
                <small>Available tags: {%date%}, {%session_id%}, {%user_name%}</small>
            </div>
        </div>';
}

function generateNodeHeader($type, $config) {
    return '
        <div class="node-header">
            <i class="' . $config['icon'] . '"></i>
            <span>' . $config['name'] . '</span>
        </div>';
}

function generateStartActionContent() {
    return '<p>This is the entry point of your chat flow. All flows should begin with a Start Action.</p>';
}

function generateEndActionContent() {
    return '
        <div class="end-options">
            <label><input type="checkbox" name="auto-close" value="true"> Auto close chat</label>
            <label><input type="checkbox" name="save-session" value="true"> Save session data</label>
            <label><input type="checkbox" name="show-feedback" value="true"> Show feedback form</label>
        </div>';
}

function generateGoToActionContent() {
    return '
        <div class="action-selector">
            <label>Target Action:</label>
            <select class="action-select">
                <option value="">Select an action...</option>
                <option value="welcome-flow">Welcome Flow</option>
                <option value="product-flow">Product Flow</option>
                <option value="support-flow">Support Flow</option>
            </select>
        </div>
        <div class="bypass-option">
            <label><input type="checkbox" name="bypass-checks" value="true"> Bypass activation checks</label>
        </div>';
}

function generateRecommendationsContent() {
    return '
        <div class="recommendations-config">
            <label>Number of recommendations:</label>
            <input type="number" class="rec-count" value="3" min="1" max="10">
            <label>Content type:</label>
            <select class="rec-type">
                <option value="articles">Articles</option>
                <option value="products">Products</option>
                <option value="pages">Pages</option>
                <option value="videos">Videos</option>
            </select>
        </div>';
}

function generateSharePageContent() {
    return '
        <div class="page-config">
            <label>Page URL:</label>
            <input type="url" class="page-url" placeholder="https://example.com/page">
            <label>Title:</label>
            <input type="text" class="page-title" placeholder="Page title">
            <label>Description:</label>
            <textarea class="page-description" placeholder="Page description"></textarea>
            <label>Thumbnail URL:</label>
            <input type="url" class="page-thumbnail" placeholder="https://example.com/thumbnail.jpg">
        </div>';
}

function generateShareVideoContent() {
    return '
        <div class="video-config">
            <label>Video URL:</label>
            <input type="url" class="video-url" placeholder="https://example.com/video.mp4">
            <label>Title:</label>
            <input type="text" class="video-title" placeholder="Video title">
            <label>Description:</label>
            <textarea class="video-description" placeholder="Video description"></textarea>
            <label>Thumbnail URL:</label>
            <input type="url" class="video-thumbnail" placeholder="https://example.com/thumbnail.jpg">
            <label>Duration (seconds):</label>
            <input type="number" class="video-duration" placeholder="120">
        </div>';
}

function generateSaveInsightContent() {
    return '
        <div class="insight-config">
            <label>Insight Name:</label>
            <input type="text" class="insight-name" placeholder="e.g., user_interested_in_product_x">
            <label>Value:</label>
            <input type="text" class="insight-value" placeholder="e.g., Yes, No, Maybe">
            <label>Description:</label>
            <textarea class="insight-description" placeholder="Description of this insight"></textarea>
        </div>';
}

function generateInsightConditionContent() {
    return '
        <div class="condition-config">
            <label>Insight Name:</label>
            <input type="text" class="condition-insight-name" placeholder="e.g., user_interested_in_product_x">
            <label>Operator:</label>
            <select class="condition-operator">
                <option value="equals">Equals</option>
                <option value="not_equals">Not Equals</option>
                <option value="contains">Contains</option>
                <option value="greater_than">Greater Than</option>
                <option value="less_than">Less Than</option>
            </select>
            <label>Value:</label>
            <input type="text" class="condition-value" placeholder="Value to compare against">
        </div>';
}

function generateUserConditionContent() {
    return '
        <div class="user-condition-config">
            <label>Condition Type:</label>
            <select class="user-condition-type">
                <option value="visit_count">Visit Count</option>
                <option value="session_duration">Session Duration</option>
                <option value="user_type">User Type</option>
                <option value="location">Location</option>
                <option value="device">Device Type</option>
            </select>
            <label>Operator:</label>
            <select class="user-condition-operator">
                <option value="equals">Equals</option>
                <option value="not_equals">Not Equals</option>
                <option value="greater_than">Greater Than</option>
                <option value="less_than">Less Than</option>
                <option value="contains">Contains</option>
            </select>
            <label>Value:</label>
            <input type="text" class="user-condition-value" placeholder="Value to compare against">
        </div>';
}

function generateNodeTemplate($type, $config, $content = '') {
    $ellipsisMenu = generateEllipsisMenu();
    $nodeHeader = generateNodeHeader($type, $config);
    
    return '
        <div>
            <div class="ellipsis-menu" onclick="toggleEllipsisMenu(this)">
                <i class="fas fa-ellipsis-v"></i>
                <div class="ellipsis-dropdown">
                    ' . $ellipsisMenu . '
                </div>
            </div>
            <div class="box ' . $type . '-box">
                ' . $nodeHeader . '
                <div class="node-content">
                    ' . $content . '
                </div>
            </div>
        </div>';
}
?> 