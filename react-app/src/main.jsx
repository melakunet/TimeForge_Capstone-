import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import LiveFeed from './components/LiveFeed.jsx'
import HoursChart from './components/HoursChart.jsx'
import ProjectChart from './components/ProjectChart.jsx'
import ActivityHeatmap from './components/ActivityHeatmap.jsx'

// Mount LiveFeed into admin/dashboard.php
const liveFeedEl = document.getElementById('react-live-feed')
if (liveFeedEl) {
  createRoot(liveFeedEl).render(<StrictMode><LiveFeed /></StrictMode>)
}

// Mount analytics charts into admin/reports.php
const hoursEl = document.getElementById('react-hours-chart')
if (hoursEl) {
  createRoot(hoursEl).render(<StrictMode><HoursChart /></StrictMode>)
}

const projectEl = document.getElementById('react-project-chart')
if (projectEl) {
  createRoot(projectEl).render(<StrictMode><ProjectChart /></StrictMode>)
}

const heatmapEl = document.getElementById('react-heatmap')
if (heatmapEl) {
  createRoot(heatmapEl).render(<StrictMode><ActivityHeatmap /></StrictMode>)
}
