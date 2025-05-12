<?php
function renderSearchHeader($options) {
    $title = $options['title'] ?? 'Page Title';
    $action = $options['action'] ?? $_SERVER['PHP_SELF'];
    $searchPlaceholder = $options['searchPlaceholder'] ?? 'Search...';
    $filters = $options['filters'] ?? [];
    $buttons = $options['buttons'] ?? [];
    ?>
    <div class="mb-5">
        <h1 class="text-2xl font-semibold"><?= htmlspecialchars($title) ?></h1>
        <div class="flex flex-col md:flex-row justify-between items-center mt-4 space-y-4 md:space-y-0">
            <form method="GET" action="<?= htmlspecialchars($action) ?>" class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                <input type="text" id="searchInput" name="search" placeholder="<?= htmlspecialchars($searchPlaceholder) ?>" autocomplete="off"
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                    class="p-2.5 text-base border border-gray-300 rounded-md focus:outline-none focus:border-[#156f77]">

                <?php if (!empty($filters)): ?>
                <div class="relative">
                    <button type="button" class="p-2.5 bg-[#156f77] text-white border-none rounded-md cursor-pointer flex items-center">
                        <i class="fa fa-filter mr-1"></i> Filter
                    </button>
                    <div class="hidden absolute mt-1 top-full left-0 bg-white shadow-lg rounded-md p-4 z-20 w-48">
                        <?php foreach ($filters as $label => $value): ?>
                            <label class="block p-2.5 cursor-pointer hover:bg-gray-100">
                                <input type="radio" name="filter[]" value="<?= htmlspecialchars($value) ?>"> <?= htmlspecialchars($label) ?>
                            </label>
                        <?php endforeach; ?>
                        <hr class="my-2.5 border-t border-gray-200">
                        <label class="block p-2.5 cursor-pointer hover:bg-gray-100">
                            <input type="radio" name="order" value="asc"> Ascending
                        </label>
                        <label class="block p-2.5 cursor-pointer hover:bg-gray-100">
                            <input type="radio" name="order" value="desc"> Descending
                        </label>
                        <hr class="my-2.5 border-t border-gray-200">
                        <button type="submit" class="w-full p-2.5 mt-2.5 bg-green-600 hover:bg-green-700 text-white border-none rounded-md cursor-pointer">Apply Filter</button>
                        <button type="button" class="w-full p-2.5 mt-2.5 bg-red-600 hover:bg-red-700 text-white border-none rounded-md cursor-pointer" onclick="resetFilters()">Clear Filter</button>
                    </div>
                </div>
                <?php endif; ?>
            </form>

            <div class="flex gap-3">
                <?php foreach ($buttons as $btn): ?>
                    <button class="p-2.5 bg-[#156f77] text-white border-none rounded-md cursor-pointer" onclick="location.href='<?= htmlspecialchars($btn['href']) ?>'">
                        <?= htmlspecialchars($btn['label']) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php
}
?>