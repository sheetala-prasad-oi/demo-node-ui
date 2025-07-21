<?php
// Configuration and reusable components
$nodeTypes = [
    'start' => ['name' => 'Start Action', 'icon' => 'fas fa-play', 'inputs' => 0, 'outputs' => 1],
    'end' => ['name' => 'End Action', 'icon' => 'fas fa-stop', 'inputs' => 1, 'outputs' => 0],
    'goto' => ['name' => 'Go to Action', 'icon' => 'fas fa-arrow-right', 'inputs' => 1, 'outputs' => 1],
    'message' => ['name' => 'Message', 'icon' => 'fas fa-comment', 'inputs' => 1, 'outputs' => 1],
    'single' => ['name' => 'Single Choice Question', 'icon' => 'fas fa-list-ul', 'inputs' => 1, 'outputs' => 1],
    'multiple' => ['name' => 'Multiple Choice Question', 'icon' => 'fas fa-check-square', 'inputs' => 1, 'outputs' => 1],
    'slider' => ['name' => 'Slider Question', 'icon' => 'fas fa-sliders-h', 'inputs' => 1, 'outputs' => 1],
    'recommendations' => ['name' => 'Show Recommendations', 'icon' => 'fas fa-lightbulb', 'inputs' => 1, 'outputs' => 1],
    'share-page' => ['name' => 'Share Page', 'icon' => 'fas fa-file-alt', 'inputs' => 1, 'outputs' => 1],
    'share-video' => ['name' => 'Share Video', 'icon' => 'fas fa-video', 'inputs' => 1, 'outputs' => 1],
    'save-insight' => ['name' => 'Save Insight', 'icon' => 'fas fa-save', 'inputs' => 1, 'outputs' => 1],
    'insight-condition' => ['name' => 'Insight Condition', 'icon' => 'fas fa-question-circle', 'inputs' => 1, 'outputs' => 2],
    'user-condition' => ['name' => 'User Condition', 'icon' => 'fas fa-user-check', 'inputs' => 1, 'outputs' => 2]
];

// Function to generate ellipsis menu options
function generateEllipsisMenu() {
    global $nodeTypes;
    $menu = '';
    foreach ($nodeTypes as $type => $config) {
        $menu .= '<div class="ellipsis-option" onclick="addNextNode(this, \'' . $type . '\')">' . $config['name'] . '</div>';
    }
    $menu .= '<div class="ellipsis-option" onclick="event.stopPropagation(); removeNode(this)">Remove Node</div>';
    return $menu;
}

// Function to generate start button options
function generateStartOptions() {
    global $nodeTypes;
    $options = '';
    foreach ($nodeTypes as $type => $config) {
        $options .= '<div class="start-option" onclick="add' . ucfirst(str_replace('-', '', $type)) . 'Node()">';
        $options .= '<i class="' . $config['icon'] . '"></i>';
        $options .= '<span>' . $config['name'] . '</span>';
        $options .= '</div>';
    }
    return $options;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Drawflow</title>
  <script src="dist/drawflow.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>
  <link rel="stylesheet" type="text/css" href="src/drawflow.css" />
  <link rel="stylesheet" type="text/css" href="docs/beautiful.css" />
  <link rel="stylesheet" type="text/css" href="src/style.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Quill.js WYSIWYG Editor (Free) -->
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
  <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
  
</head>
<body>
  <div class="wrapper">
    <div class="col-right">

      <div class="menu">
        <ul>
          <li onclick="editor.changeModule('Home'); changeModule(event);" class="selected">Home</li>
          <li onclick="editor.changeModule('Other'); changeModule(event);">Other Module</li>
        </ul>
      </div>

      <div id="drawflow" ondrop="drop(event)" ondragover="allowDrop(event)">
        <!-- Start Button -->
        <div class="start-button-container">
          <button class="start-button" onclick="showStartOptions()">
            <i class="fas fa-plus"></i>
            <span>Start</span>
          </button>
          <div class="start-options" id="start-options">
            <?php echo generateStartOptions(); ?>
          </div>
        </div>

        <div class="btn-export" onclick="exportJSON()">Export</div>
        <div class="btn-clear" onclick="editor.clearModuleSelected()">Clear</div>
        <div class="btn-load" onclick="loadFlow()">Load</div>
        <div class="btn-lock">
          <i id="lock" class="fas fa-lock" onclick="editor.editor_mode='fixed'; changeMode('lock');"></i>
          <i id="unlock" class="fas fa-lock-open" onclick="editor.editor_mode='edit'; changeMode('unlock');" style="display:none;"></i>
        </div>
        <div class="bar-zoom">
          <i class="fas fa-search-minus" onclick="editor.zoom_out()"></i>
          <i class="fas fa-search" onclick="editor.zoom_reset()"></i>
          <i class="fas fa-search-plus" onclick="editor.zoom_in()"></i>
        </div>
      </div>

    </div>
  </div>

<?php include 'includes/node-templates.php'; ?>
<?php include 'includes/javascript.php'; ?>

</body>
</html> 