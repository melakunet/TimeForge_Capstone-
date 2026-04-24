import { useState, useEffect } from 'react'

const BASE = '/TimeForge_Capstone'

function getColor(hours) {
  if (hours === 0)  return '#1e293b'
  if (hours < 2)   return '#0f4c81'
  if (hours < 4)   return '#1d6fa4'
  if (hours < 6)   return '#2196c8'
  if (hours < 8)   return '#38bdf8'
  return '#7dd3fc'
}

export default function ActivityHeatmap() {
  const [data, setData]       = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError]     = useState(null)
  const [tooltip, setTooltip] = useState(null)

  useEffect(() => {
    fetch(`${BASE}/api/v1/analytics.php`)
      .then(r => r.json())
      .then(j => {
        if (j.success) setData(j.heatmap ?? [])
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

  if (loading) return <div style={cardStyle}><p style={{ color: '#94a3b8' }}>Loading heatmap…</p></div>
  if (error)   return <div style={cardStyle}><p style={{ color: '#ef4444' }}>⚠️ {error}</p></div>
  if (!data.length) return (
    <div style={cardStyle}>
      <h3 style={headStyle}>📅 Activity Heatmap — Last 12 Weeks</h3>
      <p style={{ color: '#94a3b8' }}>No activity data yet.</p>
    </div>
  )

  // Group into weeks (columns of 7 days)
  const weeks = []
  for (let i = 0; i < data.length; i += 7) {
    weeks.push(data.slice(i, i + 7))
  }

  // Month labels — show first day of each month that appears
  const monthLabels = []
  weeks.forEach((week, wi) => {
    week.forEach(day => {
      const d = new Date(day.date)
      if (d.getDate() <= 7) {
        monthLabels[wi] = d.toLocaleString('default', { month: 'short' })
      }
    })
  })

  return (
    <div style={cardStyle}>
      <h3 style={headStyle}>📅 Activity Heatmap — Last 12 Weeks</h3>

      {/* Month labels */}
      <div style={{ display: 'flex', gap: 3, marginBottom: 4, paddingLeft: 20 }}>
        {weeks.map((_, wi) => (
          <div key={wi} style={{ width: 14, fontSize: 10, color: '#64748b', textAlign: 'center' }}>
            {monthLabels[wi] ?? ''}
          </div>
        ))}
      </div>

      <div style={{ display: 'flex', gap: 3, alignItems: 'flex-start' }}>
        {/* Day-of-week labels */}
        <div style={{ display: 'flex', flexDirection: 'column', gap: 3, marginRight: 2 }}>
          {['S','M','T','W','T','F','S'].map((d, i) => (
            <div key={i} style={{ width: 14, height: 14, fontSize: 9, color: '#64748b', lineHeight: '14px', textAlign: 'center' }}>{d}</div>
          ))}
        </div>

        {/* Grid */}
        {weeks.map((week, wi) => (
          <div key={wi} style={{ display: 'flex', flexDirection: 'column', gap: 3 }}>
            {week.map((day, di) => (
              <div
                key={di}
                onMouseEnter={e => setTooltip({ x: e.clientX, y: e.clientY, date: day.date, hours: day.hours })}
                onMouseLeave={() => setTooltip(null)}
                style={{
                  width: 14, height: 14, borderRadius: 2,
                  background: getColor(day.hours),
                  cursor: 'default',
                  border: '1px solid rgba(255,255,255,0.04)',
                }}
              />
            ))}
          </div>
        ))}
      </div>

      {/* Legend */}
      <div style={{ display: 'flex', alignItems: 'center', gap: 4, marginTop: 10 }}>
        <span style={{ fontSize: 10, color: '#64748b', marginRight: 4 }}>Less</span>
        {[0, 1, 3, 5, 8].map(h => (
          <div key={h} style={{ width: 12, height: 12, borderRadius: 2, background: getColor(h) }} />
        ))}
        <span style={{ fontSize: 10, color: '#64748b', marginLeft: 4 }}>More</span>
      </div>

      {/* Tooltip */}
      {tooltip && (
        <div style={{
          position: 'fixed', left: tooltip.x + 12, top: tooltip.y - 30,
          background: '#0f172a', border: '1px solid #334155',
          borderRadius: 6, padding: '4px 10px', fontSize: 12,
          color: '#f1f5f9', pointerEvents: 'none', zIndex: 9999,
        }}>
          {tooltip.date} — {tooltip.hours}h
        </div>
      )}
    </div>
  )
}
