<tr class="task-row">
  <td>
    <input type="text"
           name="__NAME__[description]"
           class="form-control"
           placeholder="{{ __('assets.maintenance.tasks.placeholders.description') }}"
           required>
  </td>
  <td>
    <input type="date"
           name="__NAME__[performed_at]"
           class="form-control">
  </td>
  <td>
    <input type="number"
           step="0.01" min="0"
           name="__NAME__[hours]"
           class="form-control task-hours"
           value="0">
  </td>
  <td>
    <input type="number"
           step="0.01" min="0"
           name="__NAME__[cost]"
           class="form-control task-cost"
           value="0">
  </td>
  <td>
    <input type="text"
           name="__NAME__[technician_name]"
           class="form-control"
           placeholder="{{ __('assets.maintenance.tasks.placeholders.technician') }}">
  </td>
  <td>
    <input type="text"
           name="__NAME__[notes]"
           class="form-control"
           placeholder="{{ __('assets.maintenance.tasks.placeholders.notes') }}">
  </td>
  <td class="text-center">
    <button type="button"
            class="btn btn-sm btn-outline-danger js-remove-row"
            title="{{ __('assets.maintenance.tasks.table.delete') }}">
      <i class="ri-close-line"></i>
    </button>
  </td>
</tr>
