import { useState, useEffect } from 'react'
import {
  BarChart, Bar, XAxis, YAxis, CartesianGrid,
  Tooltip, ResponsiveContainer, Cell,
} from 'recharts'

const BASE   = '/TimeForge_Capstone'
const COLORS = ['#38bdf8','#818cf8','#34d399','#f59e0b','#f472b6','#a78bfa','#fb923c','#60a5fa','#4ade80','#facc15']

export default function ProjectChart() {
  const [data, setData]       = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError]     = useState(null)

  useEffect(() => {
    fetch(`${BASE}/api/v1/analytics.php`)
      .then(r => r.json())
      .then(j => {
        if (j.success) setData(j.project_chart ?? [])
        else setError(j.message)
      })
      .catch(() => setError('Failed to load data'))
      .finally(() => setLoading(false))
  }, [])

  const cardStyle = {
    background: 'var(--color-card-bg, #1e293b)',
    border: '1px solid var(--color-border, #334155)',
    borderRadius: 12, padding: '1.5rem', marginBottom: '2rem',
  }
  const headStyle = { color: 'var(--color-accent, #38bdf8)', margin: '0 0 1.25rem', fontSize: '1.05rem' }

  // Truncate long project names for axis labels
  const tickFormatter = val => val.length > 14 ? val.slice(0, 14) + '…' : val

  if (loading) return <div style={cardStyle}><p style={{ color: '#94a3b8' }}>Loading chart…</p></div>
  if (error)   return <div style={cardStyle}><p style={{ color: '#ef4444' }}>⚠️ {error}</p></div>
  if (!data.length) return (
    <div style={cardStyle}>
      <h3 style={headStyle}>📊 Hours by Project</h3>
      <p style={{ color: '#94a3b8' }}>No project data yet.</p>
    </div>
  )

  return (
    <div style={cardStyle}>
      <h3 style={headStyle}>📊 Hours by Project</h3>
      <ResponsiveContainer width="100%" height={280}>
        <BarChart data={data} margin={{ top: 5, right: 20, left: 0, bottom: 40 }}>
          <CartesianGrid strokeDasharray="3 3" stroke="#334155" />
          <XAxis dataKey="project" tick={{ fill: '#94a3b8', fontSize: 11 }} tickLine={false}
            tickFormatter={tickFormatter} angle={-35} textAnchor="end" interval={0} />
          <YAxis tick={{ fill: '#94a3b8', fontSize: 11 }} tickLine={false} axisLine={false} unit="h" />
          <Tooltip
            contentStyle={{ background: '#1e293b', border: '1px solid #334155', borderRadius: 8, color: '#f1f5f9' }}
            formatter={v => [`${v}h`, 'Hours']}
          />
          <Bar dataKey="hours" radius={[4, 4, 0, 0]}>
            {data.map((_, i) => <Cell key={i} fill={COLORS[i % COLORS.length]} />)}
          </Bar>
        </BarChart>
      </ResponsiveContainer>
    </div>
  )
}
