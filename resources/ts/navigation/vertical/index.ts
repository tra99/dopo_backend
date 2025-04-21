let navigators = <any[]>[]
let dashboards = []

async function loadDashboardsData() {
  navigators = [];
  const query = {}
  const { data: newDashboardsData, execute } = await useApi<any>(createUrl('/v1/dashboards/tree', { query }))
  dashboards = newDashboardsData.value
  execute()
}

async function initializeNavigators() {
  await loadDashboardsData()

  for (let group of Object.keys(dashboards)) {
    navigators.push({
      heading: group,
    })
    for (let dashboard of dashboards[group]) {
      const newNavitator = {
        title: dashboard.title,
        icon: { icon: `tabler-${dashboard.icon}` },
        to: { name: 'dashboard-id', params: { id: dashboard.id } }
      }
      if (dashboard.children && dashboard.children.length > 0) {
        newNavitator['children'] = []
        for (let child of dashboard.children) {
          newNavitator['children'].push({
            title: child.title,
            icon: { icon: 'tabler-circle' },
            to: { name: 'dashboard-id', params: { id: child.id } }
          })
        }
      }
      navigators.push(newNavitator)
    }
  }

  // Add static navigation items
  navigators = [
    ...navigators, 
    ...[
      { heading: 'ការគ្រប់គ្រង' },
      {
        title: 'ផ្លាស់ប្តូរផ្ទាំងទិន្ន័យ',
        icon: { icon: 'tabler-layout-grid-add' },
        to: 'edit-dashboard'
      },
      {
        title: 'ការស្រង់ព័ត៌មាន',
        icon: { icon: 'tabler-report-search' },
        children: [
          {
            title: 'បញ្ចីការវាយតម្លៃ',
            icon: { icon: 'tabler-circle' },
            to: { name: 'assessment-survey' }
          },
          {
            title: 'ស្តង់ដារ និង សួចនករ',
            icon: { icon: 'tabler-circle' },
            to: { name: 'assessment-category' }
          }
        ]
      },
      {
        title: 'ការចុះគាំទ្រសាលារៀនគំរូ',
        icon: { icon: 'tabler-clipboard-plus' },
        children: [
          {
            title: 'បញ្ជីបេសកកម្ម',
            icon: { icon: 'tabler-circle' },
            to: { name: 'patronage-mission' }
          },
          {
            title: 'ក្រុមការងារ',
            icon: { icon: 'tabler-circle' },
            to: { name: 'patronage-participant' }
          },
          {
            title: 'តាមសាលារៀន',
            icon: { icon: 'tabler-circle' },
            to: { name: 'patronage-school' }
          },
          {
            title: 'ដំណាក់កាលនៃការគាំទ្រ',
            icon: { icon: 'tabler-circle' },
            to: { name: 'patronage-step' }
          }
        ]
      },
      {
        title: 'ព័ត៌មានសាលារៀន',
        icon: { icon: 'tabler-building-warehouse' },
        to: 'school'
      },
      {
        title: 'អ្នកប្រើប្រាស់',
        icon: { icon: 'tabler-user' },
        to: 'user'
      },
      {
        title: 'កំណត់ហេតុ',
        icon: { icon: 'tabler-news' },
        to: 'log'
      },
    ]
  ]

  return navigators
}

export default initializeNavigators
