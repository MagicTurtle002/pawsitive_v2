<?php
/**
 * TableComponent.php - Reusable table component for displaying data
 */
class TableComponent
{
    private $columns;
    private $data;
    private $tableId;
    private $tableClass;
    private $actionButtons;
    private $expandable;
    private $expandCallback;

    /**
     * Constructor for the TableComponent
     * 
     * @param array $columns Associative array with column keys and display names
     * @param array $data Array of data rows to display
     * @param string $tableId Optional ID for the table element
     * @param string $tableClass Optional CSS class for the table
     * @param array $actionButtons Optional array of action buttons for each row
     * @param bool $expandable Whether rows can be expanded for more details
     */
    public function __construct($columns, $data, $tableId = 'dataTable', $tableClass = 'staff-table', $actionButtons = [], $expandable = true, $expandCallback = null)
    {
        $this->columns = $columns;
        $this->data = $data;
        $this->tableId = $tableId;
        $this->tableClass = $tableClass;
        $this->actionButtons = $actionButtons;
        $this->expandable = $expandable;
        $this->expandCallback = $expandCallback;
    }

    /**
     * Render the table with the provided data
     */
    public function render()
    {
        ?>
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table id="<?= $this->tableId ?>" class="<?= $this->tableClass ?> w-full table-auto">
                <thead class="bg-[#007b8a] text-white">
                    <tr>
                        <?php foreach ($this->columns as $columnDisplay): ?>
                            <th class="py-3 px-4 text-left"><?= $columnDisplay ?></th>
                        <?php endforeach; ?>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($this->data)): ?>
                        <tr>
                            <td colspan="<?= count($this->columns) + 1 ?>" class="py-4 px-4 text-center">No records found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($this->data as $index => $row): ?>
                            <tr class="hover:bg-gray-50 border-b border-gray-200 cursor-pointer"
                                onclick="<?= $this->expandable ? "togglePetDetails(this)" : "" ?>">
                                <?php foreach ($this->columns as $columnKey => $columnDisplay): ?>
                                    <td class="py-3 px-4"><?= $row[$columnKey] ?? '-' ?></td>
                                <?php endforeach; ?>
                                <td class="py-3 px-4 text-center relative">
                                    <button
                                        class="three-dot-btns opacity-0 transition-opacity duration-300 hover:bg-gray-200 rounded-full p-1">
                                        <i class="fas fa-ellipsis-v text-gray-500"></i>
                                    </button>
                                    <div class="dropdown-menus hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                                        <div class="py-1">
                                            <a href="view_pet.php?id=<?= $row['PetCode'] ?>"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">View Details</a>
                                            <a href="edit_pet.php?id=<?= $row['PetCode'] ?>"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit</a>
                                            <a href="#" onclick="confirmArchive('<?= $row['PetCode'] ?>')"
                                                class="block px-4 py-2 text-sm text-yellow-600 hover:bg-gray-100">Archive</a>
                                            <a href="#" onclick="confirmDelete('<?= $row['PetCode'] ?>')"
                                                class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php if ($this->expandable): ?>
                                <!-- Expandable row for additional pet details -->
                                <tr class="hidden bg-gray-50 border-b border-gray-200">
                                    <td colspan="<?= count($this->columns) + 1 ?>" class="py-4 px-8">
                                        <?php
                                        if (is_callable($this->expandCallback)) {
                                            call_user_func($this->expandCallback, $row);
                                        } else {
                                            echo "<p class='text-gray-600 text-sm'>No additional details available.</p>";
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}

/**
 * Helper function to quickly render a table
 */
function renderTable($params)
{
    $columns = $params['columns'] ?? [];
    $data = $params['data'] ?? [];
    $tableId = $params['tableId'] ?? 'dataTable';
    $tableClass = $params['tableClass'] ?? 'staff-table';
    $actionButtons = $params['actionButtons'] ?? [];
    $expandable = $params['expandable'] ?? true;
    $expandCallback = $params['expandCallback'] ?? null;

    $table = new TableComponent($columns, $data, $tableId, $tableClass, $actionButtons, $expandable, $expandCallback);
    $table->render();
}
?>