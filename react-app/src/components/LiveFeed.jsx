import { useState, useEffect } from 'react'

const BASE = '/TimeForge_Capstone'

function elapsedStr(timerStart) {
  if (!timerStart) return null
  const secs = Math.floor((Date.now() - new Date(timerStart).getTime()) / 1000)
  const h = Math.floor(secs / 3600)
  const m = Math.floor((secs % 3600) / 60)
  const s = secs % 60
  return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`
}

function StatusDot({ status }) {
  const colors = { active: '#22c55e', idle: '#f59e0b', offline: '#6b7280', overtime: '#ef4444' }
  return (
    <span style={{
      display: 'inline-block', width: 10, height: 10,
      borderRadius: '50%', background: colors[status] ?? '#6b7280',
      marginRight: 8, flexShrink: 0,
    }} />
  )
}

function UserCard({ u }) {
  const isOvertime = u.timer_start
    ? (new Date().getTime() - new Date(u.timer_start).getTime()) > 8 * 3600 * 1000
    : false
  const dotColor    = isOvertime ? 'overtime' : u.status
  const borderColor = isOvertime ? '#ef4444'
    : u.status === 'active' ? '#22c55e'
    : u.status === 'idle'   ? '#f59e0b'
    : '#374151'

  return (
    <div style={{
      background: 'var(--color-card-bg, #1e293b)',
      border: '1px solid var(--color-border, #334155)',
      borderLeft: `3px solid ${borderColor}`,
      borderRadius: 10, padding: '0.85rem 1rem',
      display: 'flex', alignItems: 'center', gap: 12,
    }}>
      <StatusDot status={dotColor} />
      <div style={{ flex: 1, minWidth: 0 }}>
        <div style={{ fontWeight: 600, fontSize: '0.9rem', color: 'var(--color-text, #f1f5f9)', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>
          {u.name}
        </div>
        <div style={{ fontSize: '0.78rem', color: 'var(--color-text-secondary, #94a3b8)', marginTop: 2 }}>
          {u.project_name || 'No active project'}
        </div>
        <div style={{ fontSize: '0.75rem', color: 'var(--color-text-secondary, #94a3b8)', marginTop: 2 }}>
          {u.label}{isOvertime ? ' · ⚠️ Overtime' : ''}
        </div>
      </div>
      {u.status === 'active' && u.timer_start && (
        <div style={{ fontFamily: 'monospace', fontSize: '0.95rem', color: isOvertime ? '#ef4444' : '#22c55e', fontWeight: 700, flexShrink: 0 }}>
          {elapsedStr(u.timer_start)}
        </div>
      )}
    </div>
  )
}

export default function LiveFeed() {
  const [users, setUsers]     = useState([])
  const [updated, setUpdated] = useState('')
  const [error, setError]     = useState(null)
  const [, setNow]            = useState(Date.now)

  useEffect(() => {
    const fetch_ = async () => {
      try {
        const res  = await fetch(`${BASE}/api/presence.php`)
        const data = await res.json()
        if (data.success) { setUsers(data.users); setUpdated(data.ts); setError(null) }
      } catch { setError('Unable to reach server') }
    }
    fetch_()
    const poll = setInterval(fetch_, 5000)
    return () => clearInterval(poll)
  }, [])

  useEffect(() => {
    const tick = setInterval(() => setNow(Date.now()), 1000)
    return () => clearInterval(tick)
  }, [])

  const active  = users.filter(u => u.status === 'active')
  const idle    = users.filter(u => u.status === 'idle')
  const offline = users.filter(u => u.status === 'offline')

  const grid = { display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(260px, 1fr))', gap: '0.6rem' }
  const SectionLabel = ({ text, color }) => (
    <div style={{ fontSize: '0.75rem', textTransform: 'uppercase', letterSpacing: '0.05em', color, marginBottom: '0.5rem', fontWeight: 600 }}>
      ● {text}
    </div>
  )

  if (error) return <p style={{ color: '#ef4444' }}>⚠️ {error}</p>
  if (!users.length) return <p style={{ color: 'var(--color-text-secondary, #94a3b8)' }}>Loading presence data…</p>

  return (
    <div>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '1rem' }}>
        <h2 style={{ margin: 0, color: 'var(--color-accent, #38bdf8)', fontSize: '1.1rem' }}>🟢 Live Freelancer Presence</h2>
        <span style={{ fontSize: '0.78rem', color: 'var(--color-text-secondary, #94a3b8)' }}>Updated {updated}</span>
      </div>
      {active.length  > 0 && <div style={{ marginBottom: '1rem' }}><SectionLabel text={`Active Now (${active.length})`} color="#22c55e" /><div style={grid}>{active.map(u => <UserCard key={u.id} u={u} />)}</div></div>}
      {idle.length    > 0 && <div style={{ marginBottom: '1rem' }}><SectionLabel text={`Idle (${idle.length})`} color="#f59e0b" /><div style={grid}>{idle.map(u => <UserCard key={u.id} u={u} />)}</div></div>}
      {offline.length > 0 && <div><SectionLabel text={`Offline (${offline.length})`} color="#6b7280" /><div style={grid}>{offline.map(u => <UserCard key={u.id} u={u} />)}</div></div>}
    </div>
  )
}
