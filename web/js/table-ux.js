(function () {
    const SELECTOR = '.grid-view > table, table.ticket-table, table.table.table-striped';
    const STORAGE_PREFIX = 'smart-table-v1:';

    function normalizeText(value) {
        return (value || '').toString().trim().toLowerCase();
    }

    function parseComparableValue(raw) {
        const text = (raw || '').toString().replace(/\s+/g, ' ').trim();
        const numeric = text.replace(/\./g, '').replace(',', '.');
        if (/^-?\d+(\.\d+)?$/.test(numeric)) {
            return { type: 'number', value: parseFloat(numeric) };
        }
        const dateValue = Date.parse(text);
        if (!Number.isNaN(dateValue)) {
            return { type: 'date', value: dateValue };
        }
        return { type: 'text', value: normalizeText(text) };
    }

    function buildStorageKey(table, index) {
        const hostId = table.closest('[id]') ? table.closest('[id]').id : '';
        const page = window.location.pathname;
        return STORAGE_PREFIX + page + ':' + hostId + ':' + index;
    }

    function moveColumn(table, fromIndex, toIndex) {
        if (fromIndex === toIndex) {
            return;
        }

        Array.from(table.rows).forEach((row) => {
            if (!row || row.cells.length <= Math.max(fromIndex, toIndex)) {
                return;
            }
            const fromCell = row.cells[fromIndex];
            const toCell = row.cells[toIndex];
            if (!fromCell || !toCell) {
                return;
            }

            if (fromIndex < toIndex) {
                row.insertBefore(fromCell, toCell.nextSibling);
            } else {
                row.insertBefore(fromCell, toCell);
            }
        });
    }

    function ensureSortIndicator(th) {
        let indicator = th.querySelector('.smart-table-sort-indicator');
        if (!indicator) {
            indicator = document.createElement('span');
            indicator.className = 'smart-table-sort-indicator';
            th.appendChild(indicator);
        }
        return indicator;
    }

    function clearSortIndicators(headers) {
        headers.forEach((th) => {
            const indicator = th.querySelector('.smart-table-sort-indicator');
            if (indicator) {
                indicator.textContent = '';
            }
            th.removeAttribute('data-sort-dir');
        });
    }

    function sortRows(table, colIndex, asc) {
        const tbody = table.tBodies[0];
        if (!tbody) {
            return;
        }
        const rows = Array.from(tbody.rows);
        const sortableRows = rows.filter((row) => row.cells.length > colIndex);
        sortableRows.sort((a, b) => {
            const aValue = parseComparableValue(a.cells[colIndex].innerText);
            const bValue = parseComparableValue(b.cells[colIndex].innerText);

            if (aValue.type === bValue.type) {
                if (aValue.value < bValue.value) return asc ? -1 : 1;
                if (aValue.value > bValue.value) return asc ? 1 : -1;
                return 0;
            }

            const aText = normalizeText(a.cells[colIndex].innerText);
            const bText = normalizeText(b.cells[colIndex].innerText);
            if (aText < bText) return asc ? -1 : 1;
            if (aText > bText) return asc ? 1 : -1;
            return 0;
        });

        sortableRows.forEach((row) => tbody.appendChild(row));
    }

    function applySearch(table, value, emptyState) {
        const tbody = table.tBodies[0];
        if (!tbody) {
            return;
        }
        const needle = normalizeText(value);
        let visibleCount = 0;
        Array.from(tbody.rows).forEach((row) => {
            const haystack = normalizeText(row.innerText);
            const isVisible = needle === '' || haystack.indexOf(needle) !== -1;
            row.style.display = isVisible ? '' : 'none';
            if (isVisible) {
                visibleCount += 1;
            }
        });

        if (emptyState) {
            emptyState.classList.toggle('is-visible', visibleCount === 0);
        }
    }

    function createToolbar(table, onReset) {
        const wrapper = document.createElement('div');
        wrapper.className = 'smart-table-shell';
        table.parentNode.insertBefore(wrapper, table);
        wrapper.appendChild(table);

        const toolbar = document.createElement('div');
        toolbar.className = 'smart-table-toolbar';

        const info = document.createElement('div');
        info.className = 'smart-table-info';
        info.textContent = 'Drag colonne, ordina A-Z / Z-A e cerca in tempo reale';

        const actions = document.createElement('div');
        actions.className = 'smart-table-actions';

        const search = document.createElement('input');
        search.type = 'search';
        search.className = 'form-control form-control-sm smart-table-search';
        search.placeholder = 'Live search su tutti i campi...';

        const reset = document.createElement('button');
        reset.type = 'button';
        reset.className = 'smart-table-btn';
        reset.textContent = 'Reset colonne';
        reset.addEventListener('click', onReset);

        actions.appendChild(search);
        actions.appendChild(reset);
        toolbar.appendChild(info);
        toolbar.appendChild(actions);
        wrapper.insertBefore(toolbar, table);

        const emptyState = document.createElement('div');
        emptyState.className = 'smart-table-empty';
        emptyState.textContent = 'Nessun risultato per la ricerca corrente.';
        wrapper.appendChild(emptyState);

        return { search, emptyState };
    }

    function enhanceTable(table, tableIndex) {
        if (!table.tHead || !table.tBodies.length || table.dataset.tableEnhanced === '1') {
            return;
        }
        const headerRow = table.tHead.rows[0];
        if (!headerRow || headerRow.cells.length < 2) {
            return;
        }

        table.dataset.tableEnhanced = '1';

        const storageKey = buildStorageKey(table, tableIndex);
        const headers = Array.from(headerRow.cells);
        const defaultOrder = headers.map((_, idx) => idx);
        const { search, emptyState } = createToolbar(table, function () {
            localStorage.removeItem(storageKey);
            window.location.reload();
        });

        search.addEventListener('input', function () {
            applySearch(table, search.value, emptyState);
        });

        headers.forEach((th, index) => {
            th.setAttribute('draggable', 'true');
            th.dataset.colIndex = String(index);
            th.dataset.originIndex = String(index);

            th.addEventListener('dragstart', function () {
                th.classList.add('smart-table-dragging');
            });

            th.addEventListener('dragend', function () {
                th.classList.remove('smart-table-dragging');
            });

            th.addEventListener('dragover', function (event) {
                event.preventDefault();
            });

            th.addEventListener('drop', function (event) {
                event.preventDefault();
                const dragging = headerRow.querySelector('.smart-table-dragging');
                if (!dragging || dragging === th) {
                    return;
                }
                const from = Array.from(headerRow.cells).indexOf(dragging);
                const to = Array.from(headerRow.cells).indexOf(th);
                if (from < 0 || to < 0 || from === to) {
                    return;
                }
                moveColumn(table, from, to);
                const newOrder = Array.from(table.tHead.rows[0].cells).map((cell) =>
                    parseInt(cell.dataset.originIndex || '0', 10)
                );
                localStorage.setItem(storageKey, JSON.stringify(newOrder));
            });

            th.addEventListener('click', function (event) {
                const interactive = event.target.closest('a,button,input,select,textarea,label');
                if (interactive) {
                    return;
                }
                event.preventDefault();
                const current = th.getAttribute('data-sort-dir');
                const nextAsc = current !== 'asc';
                clearSortIndicators(Array.from(table.tHead.rows[0].cells));
                th.setAttribute('data-sort-dir', nextAsc ? 'asc' : 'desc');
                ensureSortIndicator(th).textContent = nextAsc ? 'A-Z' : 'Z-A';
                sortRows(table, Array.from(table.tHead.rows[0].cells).indexOf(th), nextAsc);
                applySearch(table, search.value, emptyState);
            });
        });

        try {
            const savedOrder = JSON.parse(localStorage.getItem(storageKey) || 'null');
            if (Array.isArray(savedOrder) && savedOrder.length === defaultOrder.length) {
                const currentOrder = Array.from(table.tHead.rows[0].cells).map((cell) =>
                    parseInt(cell.dataset.originIndex || '0', 10)
                );
                savedOrder.forEach((originIndex, targetIndex) => {
                    const fromIndex = currentOrder.indexOf(originIndex);
                    if (fromIndex !== -1 && fromIndex !== targetIndex) {
                        moveColumn(table, fromIndex, targetIndex);
                        const moved = currentOrder.splice(fromIndex, 1)[0];
                        currentOrder.splice(targetIndex, 0, moved);
                    }
                });
            }
        } catch (error) {
            localStorage.removeItem(storageKey);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const tables = document.querySelectorAll(SELECTOR);
        tables.forEach((table, index) => enhanceTable(table, index));
    });
})();
