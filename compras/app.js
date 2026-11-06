const modal = new bootstrap.Modal('#modal');
const form = document.getElementById('form');
const api = (a, extra = '') => `api.php?m=${MOD}&a=${a}${extra}`;

async function openForm(id = 0) {
  form.reset(); document.getElementById('err').textContent = '';
  form.id.value = id || '';
  document.getElementById('mtitle').textContent = (id ? 'Editar ' : 'Nuevo registro: ') + TITLE;
  if (id) {
    const r = await (await fetch(api('get', '&id=' + id))).json();
    for (const [k, v] of Object.entries(r.data || {})) if (form.elements[k] && k !== 'id') form.elements[k].value = v ?? '';
  }
  modal.show();
}

form.addEventListener('submit', async e => {
  e.preventDefault();
  const r = await (await fetch(api('save'), { method: 'POST', body: new FormData(form) })).json();
  if (r.ok) location.reload(); else document.getElementById('err').textContent = r.msg;
});

async function del(id) {
  if (!confirm('¿Eliminar este registro?')) return;
  const fd = new FormData(); fd.append('id', id);
  const r = await (await fetch(api('delete'), { method: 'POST', body: fd })).json();
  r.ok ? location.reload() : alert(r.msg);
}

document.getElementById('all').addEventListener('change', e =>
  document.querySelectorAll('.row-check').forEach(c => c.checked = e.target.checked));
