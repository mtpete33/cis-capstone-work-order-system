  $(document).ready(function () {

    let createFormDataLoaded = false;
    let searchFormDataLoaded = false;

    let currentUserRoleID = 0;
    let cachedStatuses = [];
    let cachedTechnicians = [];

    // Click handlers
    $(document).on('click', '.dash-card', function () {
      const panelId = $(this).data('panel');
      showPanel(panelId);
    })

    $(document).on('click', '.save-wo-btn', function () {
                  const $container = $(this).closest('.admin-actions');
                  const workOrderID = Number($container.data('work-order-id'));                
                  const assignedToUserIDRaw = $container.find('.assign-tech-select').val();
                  const currentStatusID = Number($container.find('.update-status-select').val());

                  const payload = {
                    workOrderID: workOrderID,
                    currentStatusID: currentStatusID,
                    assignedToUserID: assignedToUserIDRaw === '' ? null : Number(assignedToUserIDRaw)
                  };

                 $('#searchStatusMsg').text('Saving update...');

                fetch('/api/work_orders/update.php', {
                  method: 'POST',
                  credentials: 'include',
                  headers: {
                    'Content-Type': 'application/json'
                  },
                  body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => {
                  if (!data.ok) {
                    $('#searchStatusMsg').text(data.error || 'Failed to update work order');
                    return;
                  }

                  $('#searchStatusMsg').text('Work order updated successfully.');

                  //refresh work order recent list
                  loadWorkOrders();

                  //rerun current search so results are current
                  $('#searchWorkOrdersForm').trigger('submit');
                })
                .catch(function (err) {
                  $('#searchStatusMsg').text('Failed to update work order (network/server error)');
                  console.error(err);
                });
            });

    // Helper functions
    function loadStatuses() {
      return fetch('/api/meta/statuses.php', { credentials: 'include' })
      .then(res => res.json())
      .then(data =>{
        if (!data.ok) throw new Error(data.error || 'Failed to load statuses');
        cachedStatuses = data.items || [];
        populateSelect($('#searchStatus'), data.items, 'statusID', 'statusName', '-- Any status --');
      });
    }

    function loadPriorities() {
      return fetch('/api/meta/priorities.php', { credentials: 'include' })
      .then(res => res.json())
      .then(data =>{
        if (!data.ok) throw new Error(data.error || 'Failed to load priorities');
        populateSelect($('#searchPriority'), data.items, 'priorityID', 'priorityName', '-- Any priority --');
      });
    }

    function loadSearchFormData() {
      $('#searchStatusMsg').text('Loading search filters...');

      const requests = [loadStatuses(), loadPriorities()];

      if(currentUserRoleID === 1) {
        requests.push(loadTechnicians());
      }

      Promise.all(requests)
      .then(function () {
        searchFormDataLoaded = true;
        $('#searchStatusMsg').text('');
      })
      .catch(function (err) {
        $('#searchStatusMsg').text('Failed to load search filters.');
        console.error(err);
      })
    }

    function escapeHtml(str) {
      return String(str ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
    }

    function formatDate(dateStr) {
      if (!dateStr) return '';

      const d = new Date(dateStr);

      return d.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit'
      });
    }

    function getRoleLabel(roleID) {
      if (roleID === 1) return 'Admin';
      if (roleID === 2) return 'Technician';
      if (roleID === 3) return 'Requester';
      return 'User';
    }

    function showPanel(panelId) {
      $('.dashboard-panel').removeClass('active-panel').addClass('hidden-panel');
      $('.dash-card').removeClass('active');
      $('#' + panelId).removeClass('hidden-panel').addClass('active-panel');
      $('.dash-card[data-panel="' + panelId + '"]').addClass('active');

      if (panelId === 'createPanel' && !createFormDataLoaded) {
        loadCreateFormData();
      }

      if (panelId === 'createPanel') {
        $('#formStatus').text('');
      }

      if (panelId === 'searchPanel' && !searchFormDataLoaded) {
        loadSearchFormData();
      }

    }




    function loadWorkOrders() {
      $.getJSON('/api/work_orders/list.php')
        .done(function (d) {
          if (!d.ok) {
            $('#woStatus').text('Failed to load work orders');
            return;
          }

          const rows = d.items || [];
          if (rows.length === 0) {
            $('#woStatus').text('No work orders yet.');
            $('#woTableBody').html('');
            return;
          }

          $('#woStatus').text('');
          const html = rows.map(function (wo) {
            return `
              <tr>
                <td>${escapeHtml(wo.workOrderID)}</td>
                <td>${escapeHtml(wo.title)}</td>
                <td>${escapeHtml(wo.statusName)}</td>
                <td>${escapeHtml(wo.priorityName)}</td>
                <td>${escapeHtml(wo.locationName)}</td>
                <td>${escapeHtml(formatDate(wo.createdAt))}</td>
              </tr>
            `;
          }).join('');

          $('#woTableBody').html(html);
        })
        .fail(function () {
          $('#woStatus').text('Failed to load work orders (network/server error)');
        });
    }

    function populateSelect($select, items, valueKey, textKey, placeholder) {
      const opts = [`<option value="">${escapeHtml(placeholder)}</option>`];

      (items || []).forEach(function (item) {
        opts.push(
          `<option value="${escapeHtml(item[valueKey])}">${escapeHtml(item[textKey])}</option>`
        );
      });

      $select.html(opts.join(''));
    }

    function buildStatusOptions(selectedStatusID) {
      const options = cachedStatuses.map(function (status) {
      const selected = Number(status.statusID) === Number(selectedStatusID) ? 'selected' : '';
         return `<option value="${escapeHtml(status.statusID)}" ${selected}>${escapeHtml(status.statusName)}</option>`;
      });
      return options.join('');
    }

  function buildTechnicianOptions(selectedUserID) {
    const options = [`<option value="">-- Unassigned --</option>`];

    cachedTechnicians.forEach(function (tech) {
      const selected = Number(tech.userID) === Number(selectedUserID) ? 'selected' : '';
      const label = tech.userName && tech.userName.trim() !== '' ? tech.userName : tech.email;

      options.push(`<option value="${escapeHtml(tech.userID)}" ${selected}>${escapeHtml(label)}</option>`
                  );
    });
    return options.join('');
    }



    function loadDepartments() {
      return fetch('/api/meta/departments.php', { credentials: 'include' })
        .then(res => res.json())
        .then(data => {
          if (!data.ok) throw new Error(data.error || 'Failed to load departments');
          populateSelect($('#departmentID'), data.items, 'departmentID', 'departmentName', '-- Select department --');
        });
    }

    function loadLocations() {
      return fetch('/api/meta/locations.php', { credentials: 'include' })
        .then(res => res.json())
        .then(data => {
          if (!data.ok) throw new Error(data.error || 'Failed to load locations');
          populateSelect($('#locationID'), data.items, 'locationID', 'locationName', '-- Select location --');
        });
    }

    function loadCreateFormData() {
      $('#formStatus').text('Loading form data...');

      Promise.all([loadDepartments(), loadLocations()])
        .then(function () {
          createFormDataLoaded = true;
          $('#formStatus').text('');
        })
        .catch(function (err) {
          $('#formStatus').text('Failed to load form data.');
          console.error(err);
        });
    }

    $(document).on('submit', '#createWorkOrderForm', function (e) {
      e.preventDefault();

      $('#formStatus').text('Submitting...');

      const payload = {
        title: $('#woTitle').val().trim(),
        description: $('#woDescription').val().trim(),
        locationID: parseInt($('#locationID').val(), 10),
        priorityID: parseInt($('#priorityID').val(), 10),
        departmentID: parseInt($('#departmentID').val(), 10)
      };

      fetch('/api/work_orders/create.php', {
        method: 'POST',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
      })
        .then(res => res.json())
        .then(data => {
          if (!data.ok) {
            $('#formStatus').text(data.error || 'Failed to create work order');
            return;
          }

          $('#formStatus').text('Created! WorkOrderID = ' + data.workOrderID);

          $('#createWorkOrderForm')[0].reset();

          loadWorkOrders();
          showPanel('recentPanel');
        })
        .catch(function (err) {
          $('#formStatus').text('Failed to create work order (network/server error)');
          console.error(err);
        });
    });

    // Load technicians
    function loadTechnicians() {
      return fetch('/api/meta/technicians.php', { credentials: 'include' })
        .then(res => res.json())
        .then(data => {
          if (!data.ok) throw new Error(data.error || 'Failed to load technicians');
          cachedTechnicians = data.items || [];
        })
    }

    // Search form submission
    $(document).on('submit', '#searchWorkOrdersForm', function (e) {
      e.preventDefault();

      $('#searchStatusMsg').text('Searching...');
      const params = new URLSearchParams();

      const title = $('#searchTitle').val().trim();
      const statusID = $('#searchStatus').val();
      const priorityID = $('#searchPriority').val();

      if (title != '') params.append('title', title);
      if (statusID != '') params.append('statusID', statusID);
      if (priorityID != '') params.append('priorityID', priorityID);

      fetch('/api/work_orders/search.php?' + params.toString(), {
        credentials: 'include'
      })
      .then(res => res.json())
      .then(data => {
        if (!data.ok) {
          $('#searchStatusMsg').text(data.error || 'Failed to search work orders');
          return;
        }

        const rows = data.items || [];

        if (rows.length === 0) {
          $('#searchStatusMsg').text('No work orders found.');
          $('#searchTableBody').html('');
          return;
        }
        $('#searchStatusMsg').text('');

        const html = rows.map(function (wo) {
          let actionsHtml = '<span class="muted-text">View only</span>';

          if (currentUserRoleID === 1) {
            actionsHtml = `
            <div class="admin-actions" data-work-order-id="${escapeHtml(wo.workOrderID)}">
            <select class="assign-tech-select">
            ${buildTechnicianOptions(wo.assignedToUserID)}
            </select>
            <select class="update-status-select">
            ${buildStatusOptions(wo.currentStatusID)}
            </select>

            <button type="button" class="save-wo-btn primary-btn">Save</button>
            </div>
            `;
          }

          return `
          <tr>
            <td>${escapeHtml(wo.workOrderID)}</td>
            <td>${escapeHtml(wo.title)}</td>
            <td>${escapeHtml(wo.statusName)}</td>
            <td>${escapeHtml(wo.priorityName)}</td>
            <td>${escapeHtml(wo.locationName)}</td>
            <td>${escapeHtml(wo.assignedToName || 'Unassigned')}</td>
            <td>${escapeHtml(formatDate(wo.createdAt))}</td>
            <td>${actionsHtml}</td>
            </tr>
          `;
        }).join('');

        $('#searchTableBody').html(html);
      })
      .catch(function (err) {
        $('#searchStatusMsg').text('Failed to search work orders (network/server error)');
        console.error(err);
      });
    });

    $.ajax({
      url: '/api/auth/check.php',
      method: 'GET',
      dataType: 'json',
      xhrFields: {
        withCredentials: true
      }
    })
     .done(function (res) {
       if (!res.loggedIn) {
         window.location.href = '/login';
         return;
       }

       currentUserRoleID = Number(res.user.roleID || 0);

       // $('#whoami').text(
       //   'Logged in as ' + res.user.email + ' (roleID: ' + res.user.roleID + ')'
       // );

       $('#welcomeText').text('Welcome, ' + (res.user.userName || res.user.email));
       $('#roleBadge').text(getRoleLabel(res.user.roleID));


       $.getJSON('/api/dashboard/summary.php')
       .done(function (d){
         if (!d.ok) return;
         $('#totalWO').text(d.total);
         $('#openWO').text(d.open);
         $('#dashStatus').text('');
       })
       .fail(function () {
         $('#dashStatus').text('Failed to load dashboard summary');
       });

       loadWorkOrders();

     })
      .fail(function (){
      //Safety fallback
      window.location.href = '/login'
    });

  });