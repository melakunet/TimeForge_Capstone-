import { useState, useEffect } from 'react'
import {
  LineChart, Line, XAxis, YAxis, CartesianGrid,
  Tooltip, ResponsiveContainer,
} from 'recharts'

const BASE = '/TimeForge_Capstone'

export default function HoursChart() {
  const [data, setData]     = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError]   = useState(null)

  useEffect(() => {
    fetch(`${BASE}/api/v1/analytics.php`)
      .then(r => r.json())
      .then(j => {
        if (j.success) setData(j.hours_per_day ?? [])
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

  if (loading) return <div style={cardStyle}><p style={{ color: '#94a3b8' }}>Loading chart…</p></div>
  if (error)   return <div style={cardStyle}><p style={{ color: '#ef4444' }}>⚠️ {error}</p></div>
  if (!data.length) return (
    <div style={cardStyle}>
      <h3 style={headStyle}>📈 Hours Tracked — Last 30 Days</h3>
      <p style={{ color: '#94a3b8' }}>No time entries recorded in the last 30 days.</p>
    </div>
  )

  return (
    <div style={cardStyle}>
      <h3 style={headStyle}>📈 Hours Tracked — Last 30 Days</h3>
      <ResponsiveContainer width="100%" height={260}>
        <LineChart data={data} margin={{ top: 5, right: 20, left: 0, bottom: 5 }}>
          <CartesianGrid strokeDasharray="3 3" stroke="#334155" />
          <XAxis dataKey="day" tick={{ fill: '#94a3b8', fontSize: 11 }} tickLine={false} interval="preserveStartEnd" />
          <YAxis tick={{ fill: '#94a3b8', fontSize: 11 }} tickLine={false} axisLine={false} unit="h" />
          <Tooltip
            contentStyle={{ background: '#1e293b', border: '1px solid #334155', borderRadius: 8, color: '#f1f5f9' }}
            formatter={v => [`${v}h`, 'Hours']}
          />
          <Line type="monotone" dataKey="hours" stroke="#38bdf8" strokeWidth={2} dot={false} activeDot={{ r: 5 }} />
        </LineChart>
      </ResponsiveContainer>
    </div>
  )
}
